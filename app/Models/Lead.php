<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $table = 'leads';

    public function lastActioner()
    {
        return $this->hasOne('App\Models\User', 'id', 'last_actioned_by');
    }

    public function campaign()
    {
        return $this->hasOne('App\Models\Campaign', 'id', 'campaign_id');
    }

    public function leadData()
    {
        return $this->hasMany('App\Models\LeadData', 'lead_id', 'id');
    }

    public function appointment()
    {
        return $this->hasOne(Appointment::class, 'lead_id','id');
    }

    public function callBack()
    {
        return $this->hasOne(Callback::class, 'lead_id','id');
    }

    public function callLogs()
    {
        return $this->hasMany('App\Models\CallLog', 'lead_id', 'id');
    }
}
