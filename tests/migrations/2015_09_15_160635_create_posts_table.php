<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('title');
            $table->text('body');
        });
        $tags = App\Tag::all();
        factory(App\Post::class, 3)
           ->create()
           ->each(function($p) use ($tags)
            {
                for($i=0; $i<3; $i++)
                {
                    $p->comments()->save(factory(App\Comment::class)->make());
                }
                foreach($tags as $t)
                {
                    $p->tags()->attach($t['id']);
                }
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('posts');
    }
}
