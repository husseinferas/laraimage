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

    public function addImage($imageColumn,$requestKey)
    {
        $disk = config('laraimage.disk','public');
        $path = $this->imagesPath() ?? config('laraimage.default_path','images');
        $filename = (string)rand() .".". request()->$requestKey->extension();

        $store = Storage::disk($disk)->putFileAs($path, request()->$requestKey,$filename);
        $this->update([
            $imageColumn => [
                'disk' => $disk,
                'path' => $store
            ]
        ]);
    }

    public function deleteImage($imageColumn)
    {
        try {
            Storage::disk($this->$imageColumn['disk'])->delete($this->$imageColumn['path']);
            $this->update([$imageColumn => null]);
        } catch (\Exception $exception) {
            return false;
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
        try {
            return Storage::disk($this->$imageColumn['disk'])->url($this->$imageColumn['path']);
        } catch (\Exception $exception){
            return config('laraimage.default_image',null);
        }
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
