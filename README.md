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
* Multi Column Multi Image
* Multi Column Multi Image

which appearing in this diagram:   

![Laraimage Table](assets/laraimage_table.png?classes=400,200,shadow)

## Usage

##### Single Column Single Image

For example Category Model
* you need to add a nullable json column for your model

```bash
php artisan make:migration add_image_to_categories_table 
```

Inside the migration file:
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
     * this name of your image column in the database
    */
    public $imagesColumn = 'image';
    
    /*
     * add your image column in the casts array
    */
    protected $casts = [
        'image' => 'json'
    ];

    /*
     * the storage path your the images will stored
    */
    public function imagesPath()
    {
        return 'categories/photos/'.$this->id;
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
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License
[MIT](https://choosealicense.com/licenses/mit/)
