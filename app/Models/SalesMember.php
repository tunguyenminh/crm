<?php

namespace App\Models;

use App\Classes\Common;
use Illuminate\Database\Eloquent\Model;

class SalesMember extends Model
{
    protected $table = 'sales_members';

    public function getNameAttribute()
    {
        $name = $this->first_name . ' ' . $this->last_name;

        return trim($name);
    }

    public function getImageUrlAttribute()
    {
        $userImagePath = Common::getFolderPath('userImagePath');

        return $this->image == null ? asset($userImagePath.'default.png') : asset($userImagePath.$this->image);
    }
}
