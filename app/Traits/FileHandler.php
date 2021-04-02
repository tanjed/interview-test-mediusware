<?php


namespace App\Traits;


use Illuminate\Support\Facades\Storage;

trait FileHandler
{
    public function upload($file,$path,$name)
    {
        $file_content = file_get_contents($file);
        $file_extension = $file->getClientOriginalExtension();
        $final_url = $path.DIRECTORY_SEPARATOR.$name.".$file_extension";
        Storage::put($final_url,$file_content);
        return $final_url;
    }

}
