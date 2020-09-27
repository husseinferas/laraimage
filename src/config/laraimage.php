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
