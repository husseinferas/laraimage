<?php


namespace HusseinFeras\Laraimage\Traits;

use Illuminate\Support\Facades\Storage;

trait MultiColumnSingleImage
{

    /*
    * listen to the deleting event in the model
    * and delete the image with all the files before delete the model itself
   */
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model){
            $model->deleteAllImages();
        });
    }

    /*
      * add new image using the request key and specifying the column
      *
      * @param  string  $requestKey
      * @param  string  $imageColumn
     */
    public function addImage($imageColumn,$requestKey): void
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

    /*
    * delete the image by specifying the column
    *
    * @param  string  $imageColumn
    * @return  boolean
   */
    public function deleteImage($imageColumn): bool
    {
        try {
            Storage::disk($this->$imageColumn['disk'])->delete($this->$imageColumn['path']);
            $this->update([$imageColumn => null]);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /*
     * delete all images in all image columns
     *
    */
    public function deleteAllImages(): void
    {
        foreach ($this->getImageColumns() as $imageColumn) {
            $this->deleteImage($imageColumn);
        }
    }

    /*
     * get the image url by specifying the column
     *
     * @param  string  $imageColumn
     * @return  string image url | default image
    */
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
