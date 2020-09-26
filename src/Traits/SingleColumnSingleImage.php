<?php


namespace HusseinFeras\Laraimage\Traits;

use Illuminate\Support\Facades\Storage;

trait SingleColumnSingleImage
{

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model){
            $model->deleteImage();
        });
    }


    public function addImage($requestKey)
    {
        $disk = config('laraimage.disk','public');
        $path = $this->imagesPath() ?? config('laraimage.default_path','images');
        $filename = (string)rand() .".". request()->$requestKey->extension();

        $store = Storage::disk($disk)->putFileAs($path, request()->$requestKey,$filename);
        $this->update([
            $this->imageColumn => [
                'disk' => $disk,
                'path' => $store
            ]
        ]);
    }


    public function deleteImage()
    {
        $imageColumn = $this->imageColumn;
        try {
            Storage::disk($this->$imageColumn['disk'])->delete($this->$imageColumn['path']);
            $this->update([$imageColumn => null]);
        } catch (\Exception $exception) {
            return false;
        }
    }


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

}
