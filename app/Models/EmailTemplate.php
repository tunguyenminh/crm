<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'email_templates';

    public function creator()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }
}
