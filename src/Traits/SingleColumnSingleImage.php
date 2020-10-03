<?php


namespace HusseinFeras\Laraimage\Traits;

use Illuminate\Support\Facades\Storage;

trait SingleColumnSingleImage
{
    protected $imageColumn = 'images';

    /*
     * listen to the deleting event in the model
     * and delete the image with all the files before delete the model itself
    */
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model){
            $model->deleteImage();
        });
    }

    /*
     * add new image using the request key
     *
     * @param  string  $requestKey
    */
    public function addImage(string $requestKey) : string
    {
        $disk = config('laraimage.disk','public');
        $filename = (string)rand() .".". request()->$requestKey->extension();

        $store = Storage::disk($disk)->putFileAs($this->getImagesPath(), request()->$requestKey, $filename);
        $this->update([
            $this->imageColumn => [
                'disk' => $disk,
                'path' => $store
            ]
        ]);

        return $store;
    }

    /*
     * delete the image
     *
     * @return  boolean
    */
    public function deleteImage(): bool
    {
        $imageColumn = $this->imageColumn;
        try {
            Storage::disk($this->$imageColumn['disk'])->delete($this->$imageColumn['path']);
            $this->update([$imageColumn => null]);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /*
     * get the image url
     *
     * @return  string image url | default image
    */
    public function getImage()
    {
        $imageColumn = $this->imageColumn;
        try {
            return Storage::disk($this->$imageColumn['disk'])->url($this->$imageColumn['path']);
        } catch (\Exception $exception){
            return config('laraimage.default_image',null);
        }
    }

    /**
     * @return string
     */
    public function getImageColumn(): string
    {
        return $this->imageColumn;
    }

    /**
     * @param string $imageColumn
     */
    public function setImageColumn(string $imageColumn): void
    {
        $this->imageColumn = $imageColumn;
    }

    public function getImagesPath()
    {
        return config('laraimage.default_path','images');
    }
}
