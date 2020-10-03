<?php

namespace HusseinFeras\Laraimage\Test;

use HusseinFeras\Laraimage\Traits\SingleColumnSingleImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SingleColumnSingleImageTest extends TestCase
{
    /** @test */
    public function it_adds_image_to_model()
    {
        $disk = config('laraimage.disk', 'public');
        Storage::fake($disk);

        $model = new TestModel();
        $model->save();

        request()->merge(['image' => UploadedFile::fake()->image('image.jpg')]);

        $path = $model->addImage('image');

        $imageData = $model->getAttribute($model->getImageColumn());

        $this->assertEquals(['disk' => $disk, 'path' => $path], $imageData);

        Storage::disk($disk)->assertExists($imageData['path']);
    }

    /** @test */
    public function it_can_delete_image_from_model()
    {
        $disk = config('laraimage.disk', 'public');
        Storage::fake($disk);

        $model = new TestModel();
        $model->save();

        request()->merge(['image' => UploadedFile::fake()->image('image.jpg')]);

        $model->addImage('image');

        $imageData = $model->getAttribute($model->getImageColumn());

        Storage::disk($disk)->assertExists($imageData['path']);

        $model->deleteImage();

        $this->assertNull($model->fresh()->getAttribute($model->getImageColumn()));
        Storage::disk($disk)->assertMissing($imageData['path']);
    }

    /** @test */
    public function it_deletes_file_when_model_is_deleted()
    {
        $disk = config('laraimage.disk', 'public');
        Storage::fake($disk);

        $model = new TestModel();
        $model->save();

        request()->merge(['image' => UploadedFile::fake()->image('image.jpg')]);

        $model->addImage('image');

        $imageData = $model->getAttribute($model->getImageColumn());

        Storage::disk($disk)->assertExists($imageData['path']);

        $model->delete();

        Storage::disk($disk)->assertMissing($imageData['path']);
    }

    /** @test */
    public function it_adds_image_to_model_with_custom_image_field()
    {
        $disk = config('laraimage.disk', 'public');
        Storage::fake($disk);

        $model = new CustomFieldTestModel();
        $model->save();

        $field = $model->getImageColumn();
        $this->assertEquals('custom_image_field', $field);

        request()->merge(['image' => UploadedFile::fake()->image('image.jpg')]);

        $path = $model->addImage('image');

        $imageData = $model->getAttribute($field);

        $this->assertEquals(['disk' => $disk, 'path' => $path], $imageData);

        Storage::disk($disk)->assertExists($imageData['path']);
    }
}

class TestModel extends Model
{
    use SingleColumnSingleImage;

    public $timestamps = false;
    protected $guarded = [];

    protected $imageColumn = 'images';
}

class CustomFieldTestModel extends Model
{
    use SingleColumnSingleImage;

    public $timestamps = false;
    protected $guarded = [];
    protected $table = 'test_models';

    protected $imageColumn = 'custom_image_field';
}
