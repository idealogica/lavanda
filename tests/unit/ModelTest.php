<?php
use App\Post;

class ModelTest extends LavandaDBTestCase
{
    public function testGetName()
    {
        self::assertEquals('Post', Post::getName());
    }

    public function testGetPluralName()
    {
        self::assertEquals('Posts', Post::getPluralName());
    }

    public function testGetActionsDescriptor()
    {
        self::assertInstanceOf(
            'Idealogica\Lavanda\Descriptor\Descriptor',
            Post::getActionsDescriptor());
        self::assertCount(3, Post::getActionsDescriptor());
    }

    public function testGetStorageDescriptor()
    {
        self::assertInstanceOf(
            'Idealogica\Lavanda\Descriptor\StorageDescriptor',
            Post::getStorageDescriptor());
        self::assertCount(1, Post::getStorageDescriptor());
    }

    public function testGetListDescriptor()
    {
        self::assertInstanceOf(
            'Idealogica\Lavanda\Descriptor\PresentationDescriptor',
            Post::getListDescriptor());
        self::assertCount(4, Post::getListDescriptor());
    }

    public function testGetItemDescriptor()
    {
        self::assertInstanceOf(
            'Idealogica\Lavanda\Descriptor\PresentationDescriptor',
            Post::getItemDescriptor());
        self::assertCount(5, Post::getItemDescriptor());
    }

    public function testGetSearchDescriptor()
    {
        self::assertInstanceOf(
            'Idealogica\Lavanda\Descriptor\Descriptor',
            Post::getSearchDescriptor());
        self::assertCount(3, Post::getSearchDescriptor());
    }

    public function testGetSortDescriptor()
    {
        self::assertInstanceOf(
            'Idealogica\Lavanda\Descriptor\SortDescriptor',
            Post::getSortDescriptor());
        self::assertCount(3, Post::getSortDescriptor());
    }

    public function testGetDeleteDescriptor()
    {
        self::assertInstanceOf(
            'Idealogica\Lavanda\Descriptor\Descriptor',
            Post::getDeleteDescriptor());
        self::assertCount(2, Post::getDeleteDescriptor());
    }

    public function testCreateInstance()
    {
        self::assertInstanceOf(Post::class, Post::createInstance());
    }

    public function testCreateForm()
    {
        $form = Post::createForm();
        self::assertInstanceOf('Kris\LaravelFormBuilder\Form', $form);
        self::assertCount(6, $form->getFields());
    }

    public function testGetForm()
    {
        $form1 = Post::getForm();
        self::assertInstanceOf('Kris\LaravelFormBuilder\Form', $form1);
        $form2 = Post::getForm();
        self::assertEquals($form1, $form2);
    }

    public function testGetSearchForm()
    {
        $form1 = Post::getSearchForm();
        self::assertInstanceOf('Kris\LaravelFormBuilder\Form', $form1);
        $form2 = Post::getSearchForm();
        self::assertEquals($form1, $form2);
        self::assertTrue((bool)$form1->getField('query'));
        self::assertTrue((bool)$form1->getField('search'));
        self::assertTrue((bool)$form1->getField('reset'));
    }

    public function testGetList()
    {
        $list = Post::getList();
        self::assertInstanceOf('Illuminate\Pagination\LengthAwarePaginator', $list);
        self::assertCount(3, $list);
        self::assertEquals('3', $list[0]['id']);
        $list = Post::getList('3');
        self::assertCount(1, $list);
        self::assertEquals('3', $list[0]['id']);
        $list = Post::getList(null, 'id#asc');
        self::assertCount(3, $list);
        self::assertEquals('1', $list[0]['id']);
    }

    public function testGetItem()
    {
        $item = Post::getItem(1);
        self::assertInstanceOf('App\Post', $item);
        self::assertEquals('1', $item['id']);
        self::assertNull(Post::getItem(10));
    }

    public function testGetFormItem()
    {
        $item = Post::getFormItem(1);
        self::assertInstanceOf('App\Post', $item);
        self::assertEquals('1', $item['id']);
        self::assertNull(Post::getFormItem(10));
    }

    public function testSaveWithRelations()
    {
        // one-to-many, many-to-many
        $item = factory(App\Post::class)->make();
        $post = $item->toArray();
        $post['comments'] = [];
        $post['comments'][] = factory(App\Comment::class)->make()->toArray();
        $post['tags'] = [];
        foreach(App\Tag::all() as $t)
        {
            $post['tags'][] = $t['id'];
        }
        $item->saveWithRelations($post);
        $item = App\Post::find(4);
        self::assertInstanceOf('App\Post', $item);
        self::assertEquals('4', $item['id']);
        $tags = $item->tags;
        self::assertCount(3, $tags);
        $comments = $item->comments;
        self::assertCount(1, $comments);
        // one-to-one
        $item = factory(App\Comment::class)->make();
        $comment = $item->toArray();
        $comment['post'] = factory(App\Post::class)->make()->toArray();
        $item->saveWithRelations($comment);
        $item = App\Comment::find(11);
        self::assertEquals('11', $item['id']);
        self::assertEquals(11, App\Comment::count());
        $post = $item->post;
        self::assertEquals('5', $post['id']);
        self::assertEquals(5, App\Post::count());
        // db rollback
        $this->rollback();
    }

    public function testDeleteWithRelations()
    {
        // one-to-many, many-to-many
        App\Post::find(1)->deleteWithRelations();
        self::assertEquals(App\Post::where('id', 1)->count(), 0);
        self::assertEquals(App\Comment::where('post_id', 1)->count(), 0);
        self::assertEquals(DB::table('lv_post_tag')->where('post_id', 1)->count(), 0);
        // one-to-one
        App\Post::getDeleteDescriptor()->removeDescription('comments');
        App\Comment::getDeleteDescriptor()->add('post');
        App\Comment::find(4)->deleteWithRelations();
        self::assertEquals(App\Comment::where('id', 4)->count(), 0);
        self::assertEquals(App\Post::where('id', 2)->count(), 0);
        // db rollback
        $this->rollback();
    }
}
