<?php

namespace App;

use Carbon\Carbon;
use Idealogica\Lavanda\Model;
use Idealogica\Lavanda\Descriptor\Descriptor;
use Idealogica\Lavanda\Descriptor\SortDescriptor;
use Idealogica\Lavanda\Descriptor\PresentationDescriptor;
use Kris\LaravelFormBuilder\Form;

class Comment extends Model
{
    protected $table = 'lv_comments';

    public static function getItemsPerPage()
    {
        return 8;
    }

    public static function buildActionsDescriptor(Descriptor $descriptor)
    {
        $descriptor->
            add('create')->
            add('edit')->
            add('destroy');
    }

    public static function buildListDescriptor(PresentationDescriptor $descriptor)
    {
        $descriptor->
            add('id', 'text', '#', ['width' => '50px'])->
            add('post', 'entity', 'Post', [
                'width' => '250px',
                'model' => 'App\Post',
                'property' => 'title'])->
            add('created_at', 'text', 'Date', ['width' => '120px'])->
            add('name', 'text', 'User name', ['width' => '120px'])->
            add('body', 'text', 'Text', ['max_len' => 250])->
            addQueryBuilder(function ($query)
            {
                $query->
                    select('lv_comments.*')->
                    leftJoin('lv_posts', 'lv_comments.post_id', '=', 'lv_posts.id')->
                    with('post');
            });
    }

    public static function buildItemDescriptor(PresentationDescriptor $descriptor)
    {
        $descriptor->
            add('id', 'text', '#')->
            add('post', 'entity', 'Post', [
                'model' => 'App\Post',
                'property' => 'title'])->
            add('created_at', 'text', 'Date')->
            add('name', 'text', 'User name')->
            add('email', 'text', 'User email')->
            add('body', 'text', 'Text')->
            addQueryBuilder(function ($query)
            {
                $query->with('post');
            });
    }

    public static function buildSearchDescriptor(Descriptor $descriptor)
    {
        $descriptor->
            add('lv_comments.name')->
            add('lv_comments.email')->
            add('lv_comments.body');
    }

    public static function buildSortDescriptor(SortDescriptor $descriptor)
    {
        $descriptor->
            add('id', '#')->
            add('created_at', 'Date')->
            add('name', 'Name')->
            add('email', 'E-mail')->
            add('posts.title', 'Post');
    }

    public static function buildForm(Form $form, $config)
    {
        if(!$config)
        {
            $form->add('post_id', 'entity', [
                'label' => 'Post',
                'class' => 'App\Post',
                'property' => 'title',
                'empty_value' => 'Please select a post',
                'rules' => 'required',
                'required' => true]);
        }
        $form->add('created_at', 'date', [
            'label' => 'Date',
            'rules' => 'required|date',
            'required' => true,
            'default_value' => Carbon::now()->format('y-m-d')])->
        add('name', 'text', [
            'label' => 'User name',
            'rules' => 'required|min:3',
            'required' => true])->
        add('email', 'text', [
            'label' => 'User e-mail',
            'rules' => 'required|email',
            'required' => true])->
        add('body', 'textarea', [
            'label' => 'Text',
            'rules' => 'required|max:5000|min:5',
            'required' => true]);
    }

    /**
     * Get the post that owns the comment.
     */
    public function post()
    {
        return $this->belongsTo('App\Post');
    }
}
