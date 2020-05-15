<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';

    protected $dates = ['appointment_time'];

    public function lead()
    {
        return $this->hasOne(Lead::class, 'id', 'lead_id');
    }
}
