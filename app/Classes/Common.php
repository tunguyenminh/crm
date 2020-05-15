<?php

namespace App\Classes;

use App\Models\LeadData;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

/**
 * Class Reply
 * @package App\Classes
 */
class Common
{
    public static function getFolderPath($type = null)
    {
        $paths = [
            'companyLogoPath' => 'assets/uploads/companies/',
            'userImagePath' => 'assets/uploads/users/'
        ];

        return ($type == null) ? $paths : $paths[$type];
    }

    public static function year()
    {
        return Carbon::now()->format('Y');
    }

    public static function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    public static function deleteCommonFiles($filePath)
    {
        $fileArray = explode('/', $filePath);

        $image = $fileArray[count($fileArray) - 1];
        $imageArray = explode('.', $image);
        $imageName = $imageArray[0];
        $extension = $imageArray[1];

        array_pop($fileArray);

        $dirString = implode('/', $fileArray);
        $rootDir = $dirString;

        // Create an output file path from the size and the filename.
        $outputFile = $rootDir .'/'. $imageName. '_*x*'.'.'.$extension;

        $imageFiles = File::glob($outputFile);
        foreach($imageFiles as $imageFile){
            File::delete($imageFile);
        }
        //Deleting Orginal Files
        File::delete($filePath);
    }

    public static function formatCurrency($currencySymbol, $amount)
    {
        return $currencySymbol . number_format($amount, 2);
    }

    public static function getUserWidget($user)
    {
        return '<a href="#" class="font-weight-600">'.
            '<img src="'.$user->image_url.'" alt="avatar" width="30" class="rounded-circle mr-1"> '.$user->name .'</a>';
    }

    public static function secondsToStr ($duration)
    {
        $periods = array(
            'D' => 86400,
            'H' => 3600,
            'M' => 60,
            'S' => 1
        );

        $parts = array();

        foreach ($periods as $name => $dur) {
            $div = floor($duration / $dur);

            if ($div == 0)
                continue;
            else
                if ($div == 1)
                    $parts[] = $div . " " . $name;
                else
                    $parts[] = $div . " " . $name;
            $duration %= $dur;
        }

        $last = array_pop($parts);

        if (empty($parts))
            return $last;
        else
            return join(', ', $parts) . ", " . $last;
    }

    public static function secondsToStrFull ($duration)
    {
        $periods = array(
            'Day' => 86400,
            'Hour' => 3600,
            'Minute' => 60,
            'Second' => 1
        );

        $parts = array();

        foreach ($periods as $name => $dur) {
            $div = floor($duration / $dur);

            if ($div == 0)
                continue;
            else
                if ($div == 1)
                    $parts[] = $div . " " . $name;
                else
                    $parts[] = $div . " " . $name . "s";
            $duration %= $dur;
        }

        $last = array_pop($parts);

        if (empty($parts))
            return $last;
        else
            return join(', ', $parts) . ", " . $last;
    }

    public static function getLeadDataByColumn($leadId, $columnValues)
    {
        $leadData = LeadData::where('lead_id', $leadId)
            ->whereIn('field_name', $columnValues)
            ->first();

        return $leadData ? $leadData->field_value : '';
    }
}