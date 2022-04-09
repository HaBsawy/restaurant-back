<?php


namespace App\Helper;


use Illuminate\Support\Facades\Storage;

class UploadHelper
{
    public static function upload($path, $file, $name = null)
    {
        $name = $name ?
            $name . '.' . $file->getClientOriginalExtension() :
            time() . '_' . rand(1000,9999) . '.' . $file->getClientOriginalExtension();
        return Storage::disk('public')->putFileAs($path, $file, $name);
    }

    public static function delete($path)
    {
        Storage::disk('public')->delete($path);
    }

    public static function deleteDirectory($path)
    {
        Storage::disk('public')->deleteDirectory($path);
    }
}
