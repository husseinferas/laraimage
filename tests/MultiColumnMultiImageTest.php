<?php

namespace HusseinFeras\Laraimage\Test;

use HusseinFeras\Laraimage\Traits\MultiColumnMultiImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MultiColumnMultiImageTest extends TestCase
{
    /** @test */
    public function it_adds_images_to_model()
    {
        $disk = config('laraimage.disk', 'public');
        Storage::fake($disk);

        $model = new MCMIModel();
        $model->save();

        $postersInput = [];
        array_push($postersInput,UploadedFile::fake()->image('image.jpg'));
        array_push($postersInput,UploadedFile::fake()->image('image2.jpg'));

        $coversInput = [];
        array_push($coversInput,UploadedFile::fake()->image('image3.jpg'));
        array_push($coversInput,UploadedFile::fake()->image('image4.jpg'));

        request()->merge(['posters' => $postersInput]);
        request()->merge(['covers' => $coversInput]);

        $posters = $model->addImages('poster','posters');
        $covers = $model->addImages('cover','covers');

        $postersData = $model->getAttribute('poster');
        $coversData = $model->getAttribute('cover');

        $this->assertEquals($posters, $postersData);
        $this->assertEquals($covers, $coversData);

        foreach ($postersData as $postersDatum){
            Storage::disk($disk)->assertExists($postersDatum['path']);
        }
        foreach ($coversData as $coversDatum){
            Storage::disk($disk)->assertExists($coversDatum['path']);
        }

        //append

        $postersInput = [];
        array_push($postersInput,UploadedFile::fake()->image('image.jpg'));
        array_push($postersInput,UploadedFile::fake()->image('image2.jpg'));

        $coversInput = [];
        array_push($coversInput,UploadedFile::fake()->image('image3.jpg'));
        array_push($coversInput,UploadedFile::fake()->image('image4.jpg'));

        request()->merge(['posters' => $postersInput]);
        request()->merge(['covers' => $coversInput]);

        $posters = $model->addImages('poster','posters',true);
        $covers = $model->addImages('cover','covers',true);

        $postersData = $model->getAttribute('poster');
        $coversData = $model->getAttribute('cover');

        $this->assertEquals($posters, $postersData);
        $this->assertEquals($covers, $coversData);

        foreach ($postersData as $postersDatum){
            Storage::disk($disk)->assertExists($postersDatum['path']);
        }
        foreach ($coversData as $coversDatum){
            Storage::disk($disk)->assertExists($coversDatum['path']);
        }
    }


    /** @test */
    public function it_deletes_file_when_model_is_deleted()
    {
        $disk = config('laraimage.disk', 'public');
        Storage::fake($disk);

        $model = new MCMIModel();
        $model->save();

        $postersInput = [];
        array_push($postersInput,UploadedFile::fake()->image('image.jpg'));
        array_push($postersInput,UploadedFile::fake()->image('image2.jpg'));

        $coversInput = [];
        array_push($coversInput,UploadedFile::fake()->image('image3.jpg'));
        array_push($coversInput,UploadedFile::fake()->image('image4.jpg'));

        request()->merge(['posters' => $postersInput]);
        request()->merge(['covers' => $coversInput]);

        $posters = $model->addImages('poster','posters');
        $covers = $model->addImages('cover','covers');

        $postersData = $model->getAttribute('poster');
        $coversData = $model->getAttribute('cover');

        $this->assertEquals($posters, $postersData);
        $this->assertEquals($covers, $coversData);

        foreach ($postersData as $postersDatum){
            Storage::disk($disk)->assertExists($postersDatum['path']);
        }
        foreach ($coversData as $coversDatum){
            Storage::disk($disk)->assertExists($coversDatum['path']);
        }

        $model->deleteImages('poster');
        $model->deleteImages('cover');


        foreach ($coversData as $coversDatum){
            Storage::disk($disk)->assertMissing($coversDatum['path']);
        }
        foreach ($postersData as $postersDatum){
            Storage::disk($disk)->assertMissing($postersDatum['path']);
        }

        //append

        $postersInput = [];
        array_push($postersInput,UploadedFile::fake()->image('image.jpg'));
        array_push($postersInput,UploadedFile::fake()->image('image2.jpg'));

        $coversInput = [];
        array_push($coversInput,UploadedFile::fake()->image('image3.jpg'));
        array_push($coversInput,UploadedFile::fake()->image('image4.jpg'));

        request()->merge(['posters' => $postersInput]);
        request()->merge(['covers' => $coversInput]);

        $posters = $model->addImages('poster','posters');
        $covers = $model->addImages('cover','covers');

        $postersData = $model->getAttribute('poster');
        $coversData = $model->getAttribute('cover');

        $this->assertEquals($posters, $postersData);
        $this->assertEquals($covers, $coversData);

        foreach ($postersData as $postersDatum){
            Storage::disk($disk)->assertExists($postersDatum['path']);
        }
        foreach ($coversData as $coversDatum){
            Storage::disk($disk)->assertExists($coversDatum['path']);
        }


        $model->deleteImages('poster',$postersData[0]['id']);
        $model->deleteImages('cover',$coversData[0]['id']);

        $this->assertEquals(count($model->getImages('poster')),1);
        $this->assertEquals(count($model->getImages('cover')),1);

        Storage::disk($postersData[0]['disk'])->assertMissing($postersData[0]['path']);
        Storage::disk($postersData[1]['disk'])->assertExists($postersData[1]['path']);
        Storage::disk($coversData[0]['disk'])->assertMissing($coversData[0]['path']);
        Storage::disk($coversData[1]['disk'])->assertExists($coversData[1]['path']);
    }
}

class MCMIModel extends Model
{
    use MultiColumnMultiImage;
    protected $table = 'test_models';

    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'poster' => 'json',
        'cover' => 'json'
    ];

    protected $imagesColumns = ['poster','cover'];
}

