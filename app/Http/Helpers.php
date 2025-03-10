<?php
use Illuminate\Support\Facades\Storage;
if (! function_exists('getFileUrl')) {
    function getFileUrl($file_name)
    {
        return $file_name ? env('APP_URL') . Storage::url($file_name) : null;
    }
}

 
function american_date($date)
{
    if ($date) {
        return Carbon::parse($date)->format('m-d-Y');
    }
    return '';
}
