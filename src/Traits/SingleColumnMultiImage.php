<?php


namespace HusseinFeras\Laraimage\Traits;

use Illuminate\Support\Facades\Storage;

trait SingleColumnMultiImage
{

    /*
     * listen to the deleting event in the model
     * and delete all the images with all the files before delete the model itself
    */
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model){
            $model->deleteImages();
        });
    }

    /*
     * add new images using the request key
     * the $append flag decide with you want to overwrite the existing images in the database or append to them
     *
     * @param  string  $requestKey
     * @param  bool  $append
    */
    public function addImages($requestKey,$append = false) : void
    {
        $imagesColumn = $this->imagesColumn;
        $disk = config('laraimage.disk','public');
        $path = $this->imagesPath() ?? config('laraimage.default_path','images');
        $images = [];

        if (empty($append)) //add new images
        {
            foreach (request()->$requestKey as $image) {
                $id = (string)rand();
                $store = Storage::disk($disk)->putFileAs($path, $image, $id . '.' . $image->extension());
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
                $store = Storage::disk($disk)->putFileAs($path, $image, $id . '.' . $image->extension());
                $appendedImages[] = [
                    'id' => $id,
                    'disk' => $disk,
                    'path' => $store
                ];
            }
            $this->update([$imagesColumn => $appendedImages]);
        }
    }


    /*
     * delete images
     * if $id is null this function will delete all the images in the images column
     * if $id is the value of id of an image then this image only will delete
     *
     * @param  integer  $id
     * @return  boolean
    */
    public function deleteImages($id = null): bool
    {
        $imagesColumn = $this->imagesColumn;
        if (empty($this->$imagesColumn) || is_null($this->$imagesColumn)) return false;

        if (empty($id)) //delete all images
        {
            try {
                foreach ($this->$imagesColumn as $image)
                {
                    Storage::disk($image['disk'])->delete($image['path']);
                }
                $this->update([$imagesColumn => null ]);
                return true;
            } catch (\Exception $exception) {
                return false;
            }
        }
        else //delete single image by id
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
     * get the images urls
     *
     * @return  array  images url | default image
    */
    public function getImages(): array
    {
        $imagesColumn = $this->imagesColumn;
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
     * @return string
     */
    public function getImageColumn(): string
    {
        return $this->imagesColumn;
    }

    /**
     * @param string $imagesColumn
     */
    public function setImageColumn(string $imagesColumn): void
    {
        $this->imagesColumn = $imagesColumn;
    }

}
