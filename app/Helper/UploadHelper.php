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
        return Storage::disk(self::getDisk())->putFileAs($path, $file, $name);
    }

    public static function delete($path)
    {
        Storage::disk(self::getDisk())->delete($path);
    }

    public static function deleteDirectory($path)
    {
        Storage::disk(self::getDisk())->deleteDirectory($path);
    }

    private static function getDisk(): string
    {
        return app()->runningUnitTests() ? 'test' : 'public';
    }
}
