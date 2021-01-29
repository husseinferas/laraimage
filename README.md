<p align="center">
<img width="200" src="assets/laraimage_logo.svg" alt="Laraimage logo"></p>
<h3 align="center">Laraimage</h3>
<p align="center">A Laravel package that adds a simple image functionality to any Laravel model</p>
<br>

<hr>


## Documentation

* [Installation](#installation)
* [Getting started](#getting-started)
* [Usage](#usage)
* [Configuration](#configuration)
* [Contributing](#contributing)
* [License](#license)

## Installation

Install Laraimage using [composer](https://getcomposer.org/).


```
composer require husseinferas/laraimage
```


## Getting started

Laraimage served four use cases when using images in web applications and backend services:
* Single Column Single Image
* Single Column Multi Image
* Multi Column Single Image
* Multi Column Multi Image

which appearing in this diagram:   

<img src="assets/laraimage_table.png" width="450" alt="Laraimage diagram">

##### The json structure of the single image is:
```json
{
 "disk": "public", 
 "path": "path"
}
```

##### The json structure of the multi image is:
```json
{
 "id": 1093742,
 "disk": "public", 
 "path": "path"
}
```

**Note:** you can change the disk of your filesystem without effect the old files because the disk already stored with each image

## Usage

### Single Column Single Image

For example Category Model
* you need to add a nullable json column for your model

```bash
php artisan make:migration add_image_to_categories_table 
```

* the migration file:
```php
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->json('image')->nullable();
        });
    }
```

* Inside the model class:

```php
<?php

namespace App;

use HusseinFeras\Laraimage\Traits\SingleColumnSingleImage;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use SingleColumnSingleImage;

    /*
     * this is the name of your image column in the database
    */
    public $imagesColumn = 'image';
    
    /*
     * add your image column in the casts array
    */
    protected $casts = [
        'image' => 'json'
    ];

    /*
     * the storage path where the images will stored
    */
    public function imagesPath()
    {
        return 'categories/images/'.$this->id;
    }
}
```

This Trait `SingleColumnSingleImage` adds these functions that used in controller methods:
```php
    /*
     * add new image using the request key
     *
     * @param  string  $requestKey
    */
    public function addImage(string $requestKey) : void {}

    /*
     * delete the image
     *
     * @return  boolean
    */
    public function deleteImage(): bool {}
    /*
     * get the image url
     *
     * @return  string image url | default image
    */
    public function getImage() {}
```

**Note:** when you delete the model using `delete()` method this trait will listen to the deleting event and delete the image files automatically


### Single Column Multi Image

For example Products Model
* you need to add a nullable json column for your model

```bash
php artisan make:migration add_image_to_products_table 
```

* the migration file:
```php
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('images')->nullable();
        });
    }
```

* Inside the model class:

```php
<?php

namespace App;

use HusseinFeras\Laraimage\Traits\SingleColumnMultiImage;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use SingleColumnMultiImage;
    /*
     * this is the name of your images column in the database
    */
    public $imagesColumn = 'images';
       /*
        * add your image column in the casts array
       */
       protected $casts = [
           'images' => 'json'
       ];
   
       /*
        * the storage path where the images will stored
       */
       public function imagesPath()
       {
           return 'products/images/'.$this->id;
       }
}
```

This Trait `SingleColumnMultiImage` adds these functions that used in controller methods:
```php
      /*
       * add new images using the request key
       * the $append flag decide with you want to overwrite the existing images in the database or append to them
       *
       * @param  string  $requestKey
       * @param  bool  $append
      */
      public function addImages($requestKey,$append = false) : void {}
      /*
        * delete images
        * if $id is null this function will delete all the images in the images column
        * if $id is the value of id of an image then this image only will delete
        *
        * @param  integer  $id
        * @return  boolean
       */
       public function deleteImages($id = null): bool {}
       /*
        * get the images urls
        *
        * @return  array  images url | default image
       */
       public function getImages(): array {}
```

**Note:** when you delete the model using `delete()` method this trait will listen to the deleting event and delete the images files automatically




### Single Column Multi Image

For example Post Model
* you need to add tow or more nullable json columns for your model

```bash
php artisan make:migration add_image_to_posts_table 
```

* the migration file:
```php
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->json('poster')->nullable();
            $table->json('cover')->nullable();
        });
    }
```

* Inside the model class:

```php
<?php

namespace App;

use HusseinFeras\Laraimage\Traits\MultiColumnSingleImage;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use MultiColumnSingleImage;
    /*
     * this is the name of your image's columns in the database
    */
    protected $imageColumns = ['poster','cover'];
       /*
        * add your image column in the casts array
       */
       protected $casts = [
        'poster' => 'json',
        'cover' =>  'json',
       ];
   
       /*
        * the storage path where the images will stored
       */
       public function imagesPath()
       {
           return 'posts/images/'.$this->id;
       }
}
```

This Trait `MultiColumnSingleImage` adds these functions that used in controller methods:
```php
     
      /*
       * add new image using the request key and specifying the column
       *
       * @param  string  $requestKey
       * @param  string  $imageColumn
      */
      public function addImage($imageColumn,$requestKey): void {}

       /*
       * delete the image by specifying the column
       *
       * @param  string  $imageColumn
       * @return  boolean
      */
       public function deleteImage($imageColumn): bool {}

      /*
       * delete all images in all image columns
       *
      */
      public function deleteAllImages(): void {}

      /*
       * get the image url by specifying the column
       *
       * @param  string  $imageColumn
       * @return  string image url | default image
      */
      public function getImage($imageColumn) {}

```

**Note:** when you delete the model using `delete()` method this trait will listen to the deleting event and delete the images files automatically



### Multi Column Multi Image

For example Post Model
* you need to add tow or more nullable json columns for your model

```bash
php artisan make:migration add_images_to_posts_table 
```

* the migration file:
```php
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->json('slider')->nullable();
            $table->json('images')->nullable();
        });
    }
```

* Inside the model class:

```php
<?php

namespace App;

use HusseinFeras\Laraimage\Traits\MultiColumnMultiImage;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use MultiColumnMultiImage;
    /*
     * this is the name of your image's columns in the database
    */
    protected $imagesColumns = ['slider','images'];
       /*
        * add your image column in the casts array
       */
       protected $casts = [
        'slider' => 'json',
        'images' =>  'json',
       ];
   
       /*
        * the storage path where the images will stored
       */
       public function imagesPath()
       {
           return 'posts/images/'.$this->id;
       }
}
```

This Trait `MultiColumnMultiImage` adds these functions that used in controller methods:
```php
    
    /*
      * add new image using the request key and specifying the column
     * the $append flag decide with you want to overwrite the existing images in the database or append to them
     *
     * @param  string  $requestKey
     * @param  string  $imageColumn
     * @param  bool  $append
     */
    public function addImages($imagesColumn,$requestKey,$append = false) {}

     /*
      * delete images by specifying the column
      * if $id is null this function will delete all the images in the images column
      * if $id is the value of id of an image then this image only will delete
      *
      * @param  integer  $id
      * @return  boolean
     */
     public function deleteImages($imagesColumn,$id = null): bool {}

      /*
      * delete all images in all image columns
      *
     */
     public function deleteAllImages(): void {}
      
     /*
      * get the images urls
      *
      * @return  array  images url | default image
     */
     public function getImages($imagesColumn): array {}

```

**Note:** when you delete the model using `delete()` method this trait will listen to the deleting event and delete the images files automatically



## Configuration

First you need to publish the config file by this command
```bash
php artisan vendor:publish --provider="HusseinFeras\Laraimage\LaraimageServiceProvider"
```
You will find a new file in config directory called laraimage.php
```php
<?php

return [

    /*
     * Specify the disk that you want to store your images
     * the disks must be defined in laravel filesystem config
     * the default disks in laravel "local", "public", "s3"
    */
    'disk' => 'public',

    /*
     * Specify the default image
     * the value cloud be "null", a path of any image or a url for any external image
     * this default image returns when the image is not found or there is an error while getting the image
    */
    'default_image' => null,

   /*
    * Specify the default path where your images stored
    * the value must be string represent the folder where you want your images to stored inside the given disk
   */
    'default_path' => 'images'
];

```

You can change the default image URL and the default path also you can change the disk without warning about the old files because the disk is saved with each image

## Contributing

* **Logo:** designed by [Yasir Nabeel](https://github.com/YasirNabeel), Inspired by [L5Modular](https://github.com/Artem-Schander/L5Modular) logo.
* **Documentation:** Inspired by [Artem Schander](https://github.com/Artem-Schander).
* **Testing:** Inspired by [Jaanus Vapper](https://github.com/hulkur).

Pull requests are welcome. For major changes.

## License

Laraimage is licensed under the terms of the [MIT License](https://github.com/husseinferas/laraimage/blob/master/LICENSE)
(See LICENSE file for details).
