<?php

$factory->define(App\Post::class, function (Faker\Generator $faker)
{
    return [
        'title' => '_title_ '.$faker->realText($maxNbChars = 50),
        'body' => $faker->realText($maxNbChars = 500)];
});

$factory->define(App\Comment::class, function (Faker\Generator $faker)
{
    return [
        'name' => '_name_ '.$faker->name,
        'email' => $faker->email,
        'body' => $faker->realText($maxNbChars = 500)];
});

$factory->define(App\Tag::class, function (Faker\Generator $faker)
{
    return [
        'text' => $faker->realText(10)];
});
