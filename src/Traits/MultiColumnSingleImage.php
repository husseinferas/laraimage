<?php


namespace HusseinFeras\Laraimage\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait MultiColumnSingleImage
{

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model){
            $model->deleteAllImages();
        });
    }

    public function addImage($imageColumn,$path,$requestKey,$filename = null)
    {
        $disk = config('laraimage.disk');
        $filename = (is_null($filename) ? Str::random() : $filename) . ".".request()->file($requestKey)->getClientOriginalExtension();

        $store = Storage::disk($disk)->putFileAs($path, request()->file($requestKey),$filename);
        $this->update([
            $imageColumn => [
                'disk' => $disk,
                'path' => $store
            ]
        ]);
    }

    public function deleteImage($imageColumn)
    {
        if (!is_null($this->$imageColumn)) {
            Storage::disk($this->$imageColumn['disk'])->delete($this->$imageColumn['path']);
            $this->imageColumn = null;
        }
    }

    public function deleteAllImages()
    {
        foreach ($this->getImageColumns() as $imageColumn) {
            $this->deleteImage($imageColumn);
        }
    }


    public function getImage($imageColumn)
    {
        return Storage::disk($imageColumn['disk'])->url($imageColumn['path']);
    }

    /**
     * @return array
     */
    public function getImageColumns(): array
    {
        return $this->imageColumns;
    }

    /**
     * @param array $imageColumns
     */
    public function setImageColumns(array $imageColumns): void
    {
        $this->imageColumns = $imageColumns;
    }

}
