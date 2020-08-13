<?php


namespace HusseinFeras\Laraimage\Traits;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasSingleImage
{

    public function addImage($path,$requestKey,$filename = null,$columnName = null)
    {
        $disk = config('laraimage.disk');
        $columnName = is_null($columnName) ? $this->columnName : $columnName;
        $filename = (is_null($filename) ? Str::random() : $filename) . ".".request()->file($requestKey)->getClientOriginalExtension();

        $store = Storage::disk($disk)->putFileAs($path, request()->file($requestKey),$filename);
        $this->update([
            $columnName => [
                'disk' => $disk,
                'path' => $store
            ]
        ]);
    }

    public function getImage($columnName = null)
    {
        $column = is_null($columnName) ? $this->columnName : $columnName;

        if (!isset($this->$column['disk']) || !isset($this->$column['path'])) return null;
        return Storage::disk($this->$column['disk'])->url($this->$column['path']);
    }

    /**
     * @return string
     */
    public function getColumnName(): string
    {
        return $this->columnName;
    }

    /**
     * @param string $columnName
     */
    public function setColumnName(string $columnName): void
    {
        $this->columnName = $columnName;
    }

}
