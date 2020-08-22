<?php


namespace HusseinFeras\Laraimage\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait SingleColumnSingleImage
{

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model){
            $model->deleteImage();
        });
    }


    public function addImage($path,$requestKey,$filename = null)
    {
        $disk = config('laraimage.disk');
        $filename = (is_null($filename) ? Str::random() : $filename) . ".".request()->file($requestKey)->getClientOriginalExtension();

        $store = Storage::disk($disk)->putFileAs($path, request()->file($requestKey),$filename);
        $this->update([
            $this->imageColumn => [
                'disk' => $disk,
                'path' => $store
            ]
        ]);
    }

    public function deleteImage()
    {
        $column = $this->imageColumn;
        Storage::disk($this->$column['disk'])->delete($this->$column['path']);
        $this->imageColumn = null;
    }


    public function getImage()
    {
        $column = $this->imageColumn;
        return Storage::disk($this->$column['disk'])->url($this->$column['path']);
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
