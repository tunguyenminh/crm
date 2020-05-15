<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    protected $table = 'call_logs';

    public function user()
    {
        return $this->belongsTo(User::class, 'attempted_by');
    }
}
