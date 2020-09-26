<?php


namespace HusseinFeras\Laraimage\Traits;

use Illuminate\Support\Facades\Storage;

trait MultiColumnMultiImage
{

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model){
            $model->deleteAllImages();
        });
    }

    public function addImages($imagesColumn,$requestKey,$append = false)
    {
        $disk = config('laraimage.disk','public');
        $path = $this->imagesPath() ?? 'laraimage';
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



    public function deleteImages($imagesColumn,$id = null)
    {
        if (empty($this->$imagesColumn) || is_null($this->$imagesColumn)) return false;

        if (empty($id)) //delete all images
        {
            foreach ($this->$imagesColumn as $image) {
                Storage::disk($image['disk'])->delete($image['path']);
            }
            $this->update([$imagesColumn => null ]);
        }
        else //delete single by id
        {
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
        }
    }


    public function deleteAllImages()
    {
        foreach ($this->getImagesColumns() as $imagesColumn) {
            $this->deleteImages($imagesColumn);
        }
    }



    public function getImages($imagesColumn)
    {
        $urls = [];
        try {
            foreach ($this->$imagesColumn as $image)
            {
                if (!isset($image['disk'])) {
                    $urls[] = config('laraimage.default_image',null);
                } else {
                    $urls[] = Storage::disk($image['disk'])->url($image['path']);
                }
            }
            return $urls;
        } catch (\Exception $exception) {
            return config('laraimage.default_image', null);
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

}
