# lavanda
Administrator control panel for Laravel application.

Installation

1. Add Lavanda service provider in config/app.php:
Lavanda\LavandaServiceProvider::class

2. Publish Lavanda assets:
php artisan vendor:publish --provider 'Idealogica\Lavanda\LavandaServiceProvider' --tag=public

And if neccesary config, lang and views:

php artisan vendor:publish --provider 'Idealogica\Lavanda\LavandaServiceProvider' --tag=config
php artisan vendor:publish --provider 'Idealogica\Lavanda\LavandaServiceProvider' --tag=lang
php artisan vendor:publish --provider 'Idealogica\Lavanda\LavandaServiceProvider' --tag=views

Coding style

PSR-2 is used with these violations:

1. All kinds of braces must be placed in following way:

* if open brace placed on new line then close brace placed on new line too:

$var = 
[
    1 => 'new',
    2 => 'line',
    3 => 'placement'
];

* if open brace stays on same line then close brace placed on last line of the block:

$var = [
    1 => 'new',
    2 => 'line',
    3 => 'placement'];
    
2. There must not be one space after the control structure keyword.