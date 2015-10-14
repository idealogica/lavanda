<?php

namespace App;

use Idealogica\Lavanda\Model;
use Idealogica\Lavanda\Descriptor\Descriptor;
use Idealogica\Lavanda\Descriptor\SortDescriptor;
use Idealogica\Lavanda\Descriptor\PresentationDescriptor;
use Kris\LaravelFormBuilder\Form;
use Illuminate\Database\Eloquent\Builder;

class Tag extends Model
{
    protected $table = 'lv_tags';

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
            add('text', 'text', 'Text', ['max_len' => 100]);
    }

    public static function buildItemDescriptor(PresentationDescriptor $descriptor)
    {
        $descriptor->
            add('id', 'text', '#')->
            add('text', 'text', 'Text');
    }

    public static function buildSearchDescriptor(Descriptor $descriptor)
    {
        $descriptor->
            add('id')->
            add('text');
    }

    public static function buildSortDescriptor(SortDescriptor $descriptor)
    {
        $descriptor->
            add('id', '#')->
            add('text', 'Text');
    }

    public static function buildFormQuery(Builder $query)
    {
        $query->with('posts');
    }

    public static function buildForm(Form $form, $config)
    {
        $form->
            add('text', 'text', [
                'label' => 'Tag text',
                'rules' => 'required',
                'required' => true])->
            add('posts', 'lookup', [
                'model' => 'App\Post',
                'property' => 'title',
                'label' => 'Posts']);
    }

    public function posts()
    {
         return $this->belongsToMany('App\Post', 'lv_post_tag');
    }
}
