# Lavanda - administrator control panel

<img src="https://raw.githubusercontent.com/idealogica/lavanda/master/logo.png">

Lavanda is an administrator control panel for Laravel application.
It can be used for quick building or prototyping administrative interface of your site or service.
The main idea of Lavanda is to enhance Eloquent models to provide all required information
about your application entities and relationships in one place in unified way.
So Lavanda model (which is based on Eloquent model) is used to incorporate your app bussiness logic
and usually describes how to input, display, save and proccess your data.

Lavanda features:
* items lists viewing/searching/sorting and CRUD operations
* implementation of one-to-one, one-to-many, many-to-many relationships
* basic data types supported such as text, number, image, date
* basic controls supported such as input, date, image, fields set (one-to-one), rows set (one-to-many),
lookup field (many-to-many)
* it allows to create forms with unlimited nesting level
* it's extendable: different kinds of data types and controls can be easily added

It uses [laravel-form-builder](https://github.com/kristijanhusak/laravel-form-builder) for forms management.

### Installation

Lavanda requires the following packages: Laravel 5, kris/laravel-form-builder,
mistic100/randomcolor, idealogica/color and PHP >= 5.5.9.

Installation steps:

1. Add Lavanda to your composer.json file:
   ```
   composer require idealogica/lavanda:~1.0.0
   ```

1. Add Lavanda service provider to config/app.php:
   ```php
   Idealogica\Lavanda\LavandaServiceProvider::class
   ```

2. Publish Lavanda assets:
   ```
   php artisan vendor:publish --provider 'Idealogica\Lavanda\LavandaServiceProvider' --tag=public
   ```

3. If neccesary config, translation and views can also be published:
   ```
   php artisan vendor:publish --provider 'Idealogica\Lavanda\LavandaServiceProvider' --tag=config
   php artisan vendor:publish --provider 'Idealogica\Lavanda\LavandaServiceProvider' --tag=lang
   php artisan vendor:publish --provider 'Idealogica\Lavanda\LavandaServiceProvider' --tag=views
   ```

### Quick start

Starting point of any Lavanda application is Idealogica\Lavanda\Model class. Any
Lavanda model must be its child. It inherits form Laravel
Illuminate\Database\Eloquent\Model class and can be used in a similar way
so you can use it in both front-end and administrative parts of your application.

Any Lavanda model must override two methods listed below to provide basic information about
describing object.

##### Model::buildListDescriptor
```php
public static function buildListDescriptor(
   \Idealogica\Lavanda\Descriptor\PresentationDescriptor $descriptor)
```
Used for items list descriptor adjustment. There you should add columns to display in table
on items list page.
```php
public static function buildListDescriptor(
   \Idealogica\Lavanda\Descriptor\PresentationDescriptor $descriptor)
{
   $descriptor->
      add('id', 'text', '#', ['width' => '50px'])->
      add('created_at', 'text', 'Date', ['width' => '120px'])->
      add('title', 'text', 'Title', ['max_len' => 100])->
      add('image', 'image', 'Image', ['width' => '140px', 'img_width' => 100]);
}
```
PresentationDescriptor::add method is used to describe how to display your data in columns:
```php
public function add($name, $type = 'text', $title = '', array $parms = [])
```
Also you may add some constraints to Eloquent query builder using method PresentationDescriptor::addQueryBuilder:
```php
public function addQueryBuilder(\Closure $queryBuilder)
```
Where $queryBuilder is a closure with a `\Illuminate\Database\Eloquent\Builder $queryBuilder` as argument.
<br/><br/>
For now these types of presentaions can be used in PresentationDescriptor::add method:
* text - displays text
* image - displays image
* entity - represents sub-item in views

##### Model::buildItemDescriptor
```php
public static function buildItemDescriptor(
   \Idealogica\Lavanda\Descriptor\PresentationDescriptor $descriptor)
```
Similar to the Model::buildListDescriptor. Used for item info descriptor adjustment. There
you should add rows to display in table on item info page.
```php
public static function buildItemDescriptor(
   \Idealogica\Lavanda\Descriptor\PresentationDescriptor $descriptor)
{
   $descriptor->
      add('id', 'text', '#')->
      add('created_at', 'text', 'Date')->
      add('title', 'text', 'Title')->
      add('image', 'image', 'Image', [
         'img_width' => 600])->
      add('body', 'text', 'Text');
}
```
<br />

---

<br />
At this point if you have implelemented mehods described above you will meet all requirements
for you Lavanda model. So for now it can be used for displaying list of items and item itself.
Now you can follow **http://yourdomain/admin** adddress and test it.<br />
Perhaps you may want to continue model tweaking and override other methods to provide
ability of searching, sorting, adding and editing items.

##### Model::buildActionsDescriptor
```php
public static function buildActionsDescriptor(
   \Idealogica\Lavanda\Descriptor\Descriptor $descriptor)
```
If overridden can be used for allowing some of controller actions. By default
index (items list) and show (item info) actions of Lavanda EntityConstroller are allowed.
If you want to extend your model functionality, you can grant permissions to acces other
actions such as create, edit and destroy.
```php
public static function buildActionsDescriptor(
   \Idealogica\Lavanda\Descriptor\Descriptor $descriptor)
{
   $descriptor->
      add('create')->
      add('edit')->
      add('destroy');
}
```

##### Model::buildStorageDescriptor
```php
public static function buildStorageDescriptor(
   \Idealogica\Lavanda\Descriptor\StorageDescriptor $descriptor)
```
If overridden can be used for describing your external (non-database) storages. For example
if you want to use image data type you should describe how to store it on your hard disk.
```php
public static function buildStorageDescriptor(
   \Idealogica\Lavanda\Descriptor\StorageDescriptor $descriptor)
{
   $descriptor->
      add('image', 'image', [
         'path' => 'image/post',
         'type' => 'jpg']);
}
```
StorageDescriptor::add method is used to describe how to store your external files:
```php
public function add($name, $type = 'image', array $parms = [])
```
For now these types of storages can be used in StorageDescriptor::add method:
* image - stores/loads images from disk

##### Model::buildSearchDescriptor
```php
public static function buildSearchDescriptor(
   \Idealogica\Lavanda\Descriptor\Descriptor $descriptor)
```
If overridden can be used for describing fields to search by.
```php
public static function buildSearchDescriptor(
   \Idealogica\Lavanda\Descriptor\Descriptor $descriptor)
{
   $descriptor->
      add('id')->
      add('title')->
      add('body');
}
```

##### Model::buildSortDescriptor
```php
public static function buildSortDescriptor(
   \Idealogica\Lavanda\Descriptor\SortDescriptor $descriptor)
```
If overridden can be used for describing fields to sort by.
```php
public static function buildSortDescriptor(
   \Idealogica\Lavanda\Descriptor\SortDescriptor $descriptor)
{
   $descriptor->
      add('id', '#')->
      add('created_at', 'Date')->
      add('title', 'Title');
}
```
SortDescriptor::add method is used to describe which fields to show in 'sort by' select:
```php
public function add($name, $title = '')
```

##### Model::buildDeleteDescriptor
```php
public static function buildDeleteDescriptor(
   \Idealogica\Lavanda\Descriptor\Descriptor $descriptor)
```
If overridden can be used to enumerate relations that will be used to delete related records.
```php
public static function buildDeleteDescriptor(
   \Idealogica\Lavanda\Descriptor\Descriptor $descriptor)
{
   $descriptor->
      add('comments')->
      add('tags');
}
```

##### Model::buildFormQuery
```php
public static function buildFormQuery(
   \Illuminate\Database\Eloquent\Builder $query)
```
If overridden can be used for form data adjustment. There you may add some
constraints to Eloquent query builder to get proper value to fill form on item edit page.
```php
public static function buildFormQuery(
   \Illuminate\Database\Eloquent\Builder $query)
{
   $query->with('comments')->with('tags');
}
```

##### Model::buildForm
```php
public static function buildForm(
   \Kris\LaravelFormBuilder\Form $form, $config)
```
It's a main method of Lavanda model. If you plan to implement create and edit functions
you should override this method. If overridden it can be used for adjustment of user
input form.
```php
public static function buildForm(
   \Kris\LaravelFormBuilder\Form $form, $config)
{
   $form->
      add('created_at', 'date', [
         'label' => 'Date',
         'rules' => 'required|date',
         'required' => true,
         'default_value' => Carbon::now()->format('Y-m-d')])->
      add('title', 'text', [
         'label' => 'Post title',
         'rules' => 'required|min:5',
         'required' => true])->
      add('body', 'textarea', [
         'label' => 'Post text',
         'rules' => 'required|max:5000|min:5',
         'required' => true])->
      add('image', 'image', [
         'label' => 'Image',
         'rules' => 'required|lavanda_image:jpeg,gif,png',
         'required' => true])->
      add('tags', 'lookup', [
         'model' => 'App\Tag',
         'property' => 'text',
         'label' => 'Tags'])->
      add('comments', 'rowset', [
         'model' => 'App\Comment',
         'label' => 'Comments',
         'row_label' => 'Comment']);
}
```
Lavanda creates \Kris\LaravelFormBuilder\Form object and pases it as argument to Model::buildForm
method. Laravel-form-builder package offers various types of input controls that can be used in Lavanda.
Detailed information how to use laravel-form-builder you can find on
[its manual page](http://kristijanhusak.github.io/laravel-form-builder/).
<br /><br />
In addition to laravel-form-builder default controls Lavanda offers its own types:
* date - date input control
* image - image input control
* fieldset - sub-form from related model, one-to-one relationship
* rowset - multiple rows sub-forms for one-to-many relationship
* lookup - list of checkboxes for many-to-many relationship
<br />
<br />

---

<br />
Additionally you may want to override these methods of Lavanda model to change some of your model attributes.

##### Model::getName
```php
public static function getName()
```
Returns model name to display in UI.

##### Model::getPluralName
```php
public static function getPluralName()
```
Returns model plural name to display in UI. For English language it's not neccessary to
overrirde this method.

##### Model::hasController
```php
public static function hasController()
```
Determines if model has a custom controller. Simply return true if you have your own custom
controller to use with this model.

##### Model::getItemsPerPage
```php
public static function getItemsPerPage()
```
Returns number of list items per page.

### Examples

There are some examples of Lavanda models shipped inside test environment:

* [Blog post model](tests/models/Post.php)
* [Post tag model](tests/models/Tag.php)
* [Post comment model](tests/models/Comment.php)

### License

Lavanda is licensed under a [MIT License](https://opensource.org/licenses/MIT).
