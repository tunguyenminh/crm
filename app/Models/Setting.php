<?php

namespace App\Models;

use App\Classes\Common;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    public function getLogoUrlAttribute()
    {
        $companyLogoPath = Common::getFolderPath('companyLogoPath');

        return $this->logo == null ? asset($companyLogoPath.'default.png') : asset($companyLogoPath.$this->logo);
    }
}
