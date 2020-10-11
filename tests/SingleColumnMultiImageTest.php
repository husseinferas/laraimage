<?php

namespace HusseinFeras\Laraimage\Test;

use HusseinFeras\Laraimage\Traits\SingleColumnMultiImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SingleColumnMultiImageTest extends TestCase
{
    /** @test */
    public function it_adds_images_to_model()
    {
        $disk = config('laraimage.disk', 'public');
        Storage::fake($disk);

        $model = new SCMIModel();
        $model->save();

        $imagesInput = [];
        array_push($imagesInput,UploadedFile::fake()->image('image.jpg'));
        array_push($imagesInput,UploadedFile::fake()->image('image2.jpg'));

        request()->merge(['images' => $imagesInput]);

        $images = $model->addImages('images');

        $imageData = $model->getAttribute($model->getImagesColumn());

        $this->assertEquals($images, $imageData);

        foreach ($imageData as $imageDatum){
            Storage::disk($disk)->assertExists($imageDatum['path']);
        }

        //append

        $imagesInput = [];
        array_push($imagesInput,UploadedFile::fake()->image('image.jpg'));
        array_push($imagesInput,UploadedFile::fake()->image('image2.jpg'));

        request()->merge(['photos' => $imagesInput]);


        $images = $model->addImages('photos',true);

        $imageData = $model->getAttribute($model->getImagesColumn());

        $this->assertEquals($images, $imageData);

        foreach ($imageData as $imageDatum){
            Storage::disk($disk)->assertExists($imageDatum['path']);
        }
    }


    /** @test */
    public function it_can_delete_images_from_model()
    {
        $disk = config('laraimage.disk', 'public');
        Storage::fake($disk);

        $model = new SCMIModel();
        $model->save();

        $imagesInput = [];
        array_push($imagesInput,UploadedFile::fake()->image('image.jpg'));
        array_push($imagesInput,UploadedFile::fake()->image('image2.jpg'));

        request()->merge(['images' => $imagesInput]);

        $images = $model->addImages('images');

        $imageData = $model->getAttribute($model->getImagesColumn());

        $this->assertEquals($images, $imageData);

        foreach ($imageData as $imageDatum){
            Storage::disk($disk)->assertExists($imageDatum['path']);
        }

        $model->deleteImages();

        $this->assertNull($model->fresh()->getAttribute($model->getImagesColumn()));

        foreach ($imageData as $imageDatum){
            Storage::disk($disk)->assertMissing($imageDatum['path']);
        }

        $imagesInput = [];
        array_push($imagesInput,UploadedFile::fake()->image('image.jpg'));
        array_push($imagesInput,UploadedFile::fake()->image('image2.jpg'));

        request()->merge(['images' => $imagesInput]);

        $images = $model->addImages('images');

        $imageData = $model->getAttribute($model->getImagesColumn());

        $this->assertEquals($images, $imageData);

        foreach ($imageData as $imageDatum){
            Storage::disk($disk)->assertExists($imageDatum['path']);
        }

        $model->deleteImages($imageData[0]['id']);

        $this->assertEquals(count($model->getImages()),1);

        Storage::disk($imageData[0]['disk'])->assertMissing($imageData[0]['path']);
        Storage::disk($imageData[1]['disk'])->assertExists($imageData[1]['path']);

    }


    /** @test */
    public function it_adds_image_to_model_with_custom_image_field()
    {
        $disk = config('laraimage.disk', 'public');
        Storage::fake($disk);

        $model = new CustomFieldSCMIModel();
        $model->save();

        $field = $model->getImagesColumn();
        $this->assertEquals('custom_image_field', $field);

        $imagesInput = [];
        array_push($imagesInput,UploadedFile::fake()->image('image.jpg'));
        array_push($imagesInput,UploadedFile::fake()->image('image2.jpg'));

        request()->merge(['images' => $imagesInput]);

        $images = $model->addImages('images');

        $imageData = $model->getAttribute($model->getImagesColumn());

        $this->assertEquals($images, $imageData);

        foreach ($imageData as $imageDatum){
            Storage::disk($disk)->assertExists($imageDatum['path']);
        }
    }

}

class SCMIModel extends Model
{
    use SingleColumnMultiImage;
    protected $table = 'test_models';

    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'images' => 'json'
    ];

    protected $imagesColumn = 'images';
}

class CustomFieldSCMIModel extends Model
{
    use SingleColumnMultiImage;

    public $timestamps = false;
    protected $guarded = [];
    protected $table = 'test_models';

    protected $casts = [
        'custom_image_field' => 'json'
    ];

    protected $imagesColumn = 'custom_image_field';
}
