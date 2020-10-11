<?php

namespace HusseinFeras\Laraimage\Test;

use HusseinFeras\Laraimage\Traits\MultiColumnSingleImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MultiColumnSingleImageTest extends TestCase
{

    /** @test */
    public function it_adds_images_to_model()
    {
        $disk = config('laraimage.disk', 'public');
        Storage::fake($disk);

        $model = new MCSIModel();
        $model->save();

        request()->merge(['poster' => UploadedFile::fake()->image('image.jpg')]);
        request()->merge(['cover' => UploadedFile::fake()->image('image2.jpg')]);

        $posterPath = $model->addImage('poster','poster');
        $coverPath = $model->addImage('cover','cover');

        $posterData = $model->getAttribute('poster');
        $coverData = $model->getAttribute('cover');

        $this->assertEquals(['disk' => $disk, 'path' => $posterPath], $posterData);
        $this->assertEquals(['disk' => $disk, 'path' => $coverPath], $coverData);

        Storage::disk($disk)->assertExists($posterData['path']);
        Storage::disk($disk)->assertExists($coverData['path']);
    }


    /** @test */
    public function it_deletes_file_when_model_is_deleted()
    {
        $disk = config('laraimage.disk', 'public');
        Storage::fake($disk);

        $model = new MCSIModel();
        $model->save();

        request()->merge(['poster' => UploadedFile::fake()->image('image.jpg')]);
        request()->merge(['cover' => UploadedFile::fake()->image('image2.jpg')]);

        $posterPath = $model->addImage('poster','poster');
        $coverPath = $model->addImage('cover','cover');

        $posterData = $model->getAttribute('poster');
        $coverData = $model->getAttribute('cover');

        $this->assertEquals(['disk' => $disk, 'path' => $posterPath], $posterData);
        $this->assertEquals(['disk' => $disk, 'path' => $coverPath], $coverData);

        Storage::disk($disk)->assertExists($posterData['path']);
        Storage::disk($disk)->assertExists($coverData['path']);

        $model->deleteImage('poster');
        $model->deleteImage('cover');

        Storage::disk($disk)->assertMissing($posterData['path']);
        Storage::disk($disk)->assertMissing($coverData['path']);
    }
}

class MCSIModel extends Model
{
    use MultiColumnSingleImage;
    protected $table = 'test_models';

    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'poster' => 'json',
        'cover' => 'json'
    ];

    protected $imageColumns = ['poster','cover'];
}
