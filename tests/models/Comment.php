<?php

namespace App;

use Carbon\Carbon;
use Lavanda\Model;
use Lavanda\Descriptor\Descriptor;
use Lavanda\Descriptor\SortDescriptor;
use Lavanda\Descriptor\PresentationDescriptor;
use Kris\LaravelFormBuilder\Form;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
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
                'width' => '150px',
                'model' => 'App\Post',
                'property' => 'title'])->
            add('created_at', 'text', 'Date', ['width' => '120px'])->
            add('name', 'text', 'User name', ['width' => '120px'])->
            add('body', 'text', 'Text', ['max_len' => 100])->
            addQueryBuilder(function ($query) {
                $query->
                    select('comments.*')->
                    leftJoin('posts', 'comments.post_id', '=', 'posts.id')->
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
            addQueryBuilder(function ($query) {
                $query->with('post');
            });
    }

    public static function buildSearchDescriptor(Descriptor $descriptor)
    {
        $descriptor->
            add('name')->
            add('email')->
            add('body');
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

    public static function buildFormQuery(Builder $query)
    {
        $query->with('post');
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
                    'required' => true
                ]);
        }
        $form->add('created_at', 'date', [
                'label' => 'Date',
                'rules' => 'required',
                'required' => true,
                'default_value' => Carbon::now()->format('Y-m-d')
        ])->
        add('name', 'text', [
            'label' => 'User name',
            'rules' => 'required|min:3',
            'required' => true
        ])->
        add('email', 'text', [
            'label' => 'User e-mail',
            'rules' => 'required|email',
            'required' => true
        ])->
        add('body', 'textarea', [
            'label' => 'Text',
            'rules' => 'required|max:5000|min:5',
            'required' => true
        ]);
    }

    /**
     * Get the post that owns the comment.
     */
    public function post()
    {
        return $this->belongsTo('App\Post');
    }
}
