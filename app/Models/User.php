<?php

namespace App\Models;

use App\Classes\Common;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Trebol\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable, EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = [
        'last_active_on',
    ];

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

    public function scopeStatus($query, $status = 'enabled')
    {
        return $query->where('status', $status);
    }

    public function role()
    {
        return \DB::table('role_user')
            ->select('roles.name', 'roles.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('role_user.user_id', '=', $this->id)
            ->first();
    }

    public function updateRole($roleID)
    {
        // Deleting role before inserting
        $this->deleteRole();

        \DB::table('role_user')
            ->insert(['user_id' =>  $this->id, 'role_id'    =>  $roleID]);
    }

    public function deleteRole()
    {
        \DB::table('role_user')
            ->where('user_id', '=', $this->id)
            ->delete();
    }

    public function activeCampaigns($campaignType = 'active', $fetchType = 'all')
    {
        $campaigns = Campaign::select('campaigns.*')
                        ->join('campaign_members', 'campaign_members.campaign_id', '=', 'campaigns.id');

        if($campaignType == 'active')
        {
            $campaigns = $campaigns->where(function($query) {
                                return $query->where('campaigns.status', 'started')
                                    ->orWhereNull('campaigns.status');
                            });
        }

        if(!$this->ability('admin', 'campaign_view_all') || $fetchType != 'all')
        {
            $campaigns = $campaigns->where('campaign_members.user_id', '=', $this->id);
        }

        $campaigns = $campaigns->groupBy('campaigns.id')->get();

        return $campaigns;
    }
}
