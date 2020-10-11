<?php


namespace HusseinFeras\Laraimage\Traits;

use Illuminate\Support\Facades\Storage;

trait MultiColumnMultiImage
{

    /*
    * listen to the deleting event in the model
    * and delete all the images with all the files before delete the model itself
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
     * the $append flag decide with you want to overwrite the existing images in the database or append to them
     *
     * @param  string  $requestKey
     * @param  string  $imageColumn
     * @param  bool  $append
     */
    public function addImages($imagesColumn,$requestKey,$append = false): array
    {
        $disk = config('laraimage.disk','public');
        $images = [];

        if (empty($append)) //add new images
        {
            foreach (request()->$requestKey as $image) {
                $id = (string)rand();
                $store = Storage::disk($disk)->putFileAs($this->getImagesPath(), $image, $id . '.' . $image->extension());
                $images[] = [
                    'id' => $id,
                    'disk' => $disk,
                    'path' => $store
                ];
            }
            $this->update([$imagesColumn => $images]);
        }
        else //append to exists array images
        {
            $appendedImages = $this->$imagesColumn;

            foreach (request()->$requestKey as $image)
            {
                $id = (string)rand();
                $store = Storage::disk($disk)->putFileAs($this->getImagesPath(), $image, $id . '.' . $image->extension());
                $appendedImages[] = [
                    'id' => $id,
                    'disk' => $disk,
                    'path' => $store
                ];
            }
            $this->update([$imagesColumn => $appendedImages]);
        }
        return $appendedImages ?? $images;
    }


    /*
     * delete images by specifying the column
     * if $id is null this function will delete all the images in the images column
     * if $id is the value of id of an image then this image only will delete
     *
     * @param  integer  $id
     * @return  boolean
    */
    public function deleteImages($imagesColumn,$id = null): bool
    {
        if (empty($this->$imagesColumn) || is_null($this->$imagesColumn)) return false;

        if (empty($id)) //delete all images
        {
            try {
                foreach ($this->$imagesColumn as $image) {
                    Storage::disk($image['disk'])->delete($image['path']);
                }
                $this->update([$imagesColumn => null ]);
                return true;
            } catch (\Exception $exception) {
                return false;
            }
        }
        else //delete single by id
        {
            try {
                $keep = [];
                foreach ($this->$imagesColumn as $image)
                {
                    if ($image['id'] == $id) {
                        Storage::disk($image['disk'])->delete($image['path']);
                    } else {
                        $keep[] = $image;
                    }
                }
                $this->update([$imagesColumn => empty($keep) ? null : $keep ]);
                return true;
            } catch (\Exception $exception) {
                return false;
            }
        }
    }

    /*
     * delete all images in all image columns
     *
    */
    public function deleteAllImages(): void
    {
        foreach ($this->getImagesColumns() as $imagesColumn) {
            $this->deleteImages($imagesColumn);
        }
    }

    /*
     * get the images urls
     *
     * @return  array  images url | default image
    */
    public function getImages($imagesColumn): array
    {
        $urls = [];
        try {
            foreach ($this->$imagesColumn as $image)
            {
                if (!isset($image['disk']) || !isset($image['path'])) {
                    $urls[] = config('laraimage.default_image',null);
                } else {
                    $urls[] = Storage::disk($image['disk'])->url($image['path']);
                }
            }
            return $urls;
        } catch (\Exception $exception) {
            return [config('laraimage.default_image', null)];
        }
    }

    /**
     * @return array
     */
    public function getImagesColumns(): array
    {
        return $this->imagesColumns;
    }

    /**
     * @param array $imagesColumns
     */
    public function setImagesColumns(array $imagesColumns): void
    {
        $this->imageColumns = $imagesColumns;
    }


    public function getImagesPath()
    {
        return config('laraimage.default_path','images');
    }

}
