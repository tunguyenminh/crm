<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignMember extends Model
{
    protected $table = 'campaign_members';

    public $timestamps = false;

    protected $fillable = ['user_id', 'campaign_id'];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
