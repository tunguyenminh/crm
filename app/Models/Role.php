<?php namespace App\Models;

use Illuminate\Support\Facades\DB;
use Trebol\Entrust\EntrustRole;

class Role extends EntrustRole
{
    protected  $table = 'roles';

    protected  $role = 'App\Models\Role';

    public $timestamps = true;

    protected $guarded = ['id'];

    public function insertRolePermission($permissionID)
    {
        DB::table('permission_role')
            ->insert([
                'permission_id' => $permissionID,
                'role_id'       => $this->id
            ]);
    }

    public function deleteRolePermissions()
    {
        DB::table('permission_role')
            ->where('role_id', '=', $this->id)
            ->delete();
    }
}