<?php namespace App\models;

use Illuminate\Support\Facades\DB;
use Trebol\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    /**
     * Is Permission attached to this role
     * @param $roleID
     * @return bool
     */
    public function isPermissionAttachToRole($roleID)
    {
        $count = DB::table('permission_role')
            ->where('role_id', '=', $roleID)
            ->where('permission_id', '=', $this->id)
            ->count();

        return $count > 0 ? TRUE : FALSE ;
    }
}