<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Reply;
use App\Http\Requests\Admin\Role\IndexRequest;
use App\Http\Requests\Admin\Role\DeleteRequest;
use App\Http\Requests\Admin\Role\StoreRequest;
use App\Http\Requests\Admin\Role\UpdateRequest;
use App\models\Permission;
use App\Models\Role;

class RoleSettingController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = trans('module_settings.roleAndPermissionSettings');
        $this->pageIcon = 'fa fa-user-cog';
        $this->settingMenuActive = 'active';
        $this->roleSettingsActive = 'active';
    }

    public function index(IndexRequest $request)
    {
        return view('admin.settings.roles.index', $this->data);
    }

    public function getList()
    {
        $data = Role::select('id', 'name', 'display_name', 'description', 'created_at');

        return datatables()->eloquent($data)
            ->editColumn(
                'created_at',
                function ($row) {
                    return $row->created_at->format('d F, Y');
                }
            )
            ->addColumn('action', function ($row) {
                if($row->name == 'admin')
                {
                    return '-';
                } else {
                    return '<a href="javascript:void(0);" onclick="editModal('.$row->id.')" class="btn btn-info btn-icon icon-left mb-2"
                      data-toggle="tooltip" data-original-title="'.trans('app.edit').'"><i class="fa fa-edit" aria-hidden="true"></i></a>

                      <button onclick="deleteModal('.$row->id.')" class="btn btn-danger btn-icon icon-left mb-2"
                      data-toggle="tooltip" data-original-title="'.trans('app.delete').'"><i class="fa fa-trash" aria-hidden="true"></i></button>';
                }

            })
            ->make(true);

    }

    public function create()
    {
        $this->icon = 'plus';

        $this->roleDetails = new Role();
        $this->permission = $this->calculatePermission();

        // Call the same create view for edit
        return view('admin.settings.roles.add-edit', $this->data);
    }

    public function store(StoreRequest $request)
    {
        if($request->name == 'admin')
        {
            return Reply::error('messages.adminRoleNameNotAllowed');
        }

        \DB::beginTransaction();

        $role = new Role();
        $this->storeAndUpdate($role, $request);

        \DB::commit();
        return Reply::success('messages.createSuccess');

    }

    public function edit($id)
    {
        $this->icon = 'edit';
        $this->roleDetails = Role::find($id);
        $this->permission = $this->calculatePermission($id);

        // Call the same create view for edit
        return view('admin.settings.roles.add-edit', $this->data);
    }

    public function update(UpdateRequest $request,$id)
    {
        \DB::beginTransaction();

        $role         = Role::find($id);

        if($role->name == 'admin')
        {
            return Reply::error('messages.adminRoleNameNotAllowed');
        }

        $this->storeAndUpdate($role, $request);

        \DB::commit();
        return Reply::success('messages.updateSuccess');

    }

    public function destroy(DeleteRequest $request, $id)
    {

        $role = Role::find($id);

        if($role->name == 'admin')
        {
            return Reply::error('messages.notAllowed');
        }

        $role->delete();

        return Reply::success('messages.deleteSuccess');
    }

    protected function calculatePermission($roleID = NULL)
    {
        $allPermissions = Permission::all();

        foreach ($allPermissions as $allPermission) {
            $permissionName = $allPermission->name;
            $permissionID = $allPermission->id;

            // If edit method fired
            if($roleID != NULL)
            {
                $isAttached = $allPermission->isPermissionAttachToRole($roleID);

                $status = $isAttached ? "checked" : '';

            }else{
                $status = '';
            }

            $permission[ $permissionName ] = ['id' => $permissionID, 'status' => $status];
        }

        return $permission;
    }

    private function  storeAndUpdate($role, $request)
    {
        $role->display_name   = $request->display_name;
        $role->name   = $request->name;
        $role->description   = $request->description;

        if($role->id != NULL)
        {
            $role->deleteRolePermissions();
        }

        $role->save();

        $permissions = $request->permissions;

        if($request->permissions){
            foreach($permissions as $permission)
            {
                $role->insertRolePermission($permission);
            }
        }

    }

}
