<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {
        Model::unguard();

        \DB::beginTransaction();

        \DB::table('permissions')->delete();

        \DB::statement('ALTER TABLE permissions AUTO_INCREMENT = 1');

        //region Staff

        $permission = new Permission();
        $permission->name = 'staff_create';
        $permission->display_name = 'Staff Create';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'staff_edit';
        $permission->display_name = 'Staff Edit';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'staff_delete';
        $permission->display_name = 'Staff Delete';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'assign_role';
        $permission->display_name = 'Staff Assign Role';
        $permission->save();

        //endregion

        //region Sales Members

        $permission = new Permission();
        $permission->name = 'sales_member_create';
        $permission->display_name = 'Sales Member Create';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'sales_member_edit';
        $permission->display_name = 'Sales Member Edit';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'sales_member_delete';
        $permission->display_name = 'Sales Member Delete';
        $permission->save();

        //endregion

        //region Import Lead

        $permission = new Permission();
        $permission->name = 'import_lead';
        $permission->display_name = 'Import Lead';
        $permission->save();

        //endregion

        //region Export Lead

        $permission = new Permission();
        $permission->name = 'export_lead';
        $permission->display_name = 'Export Lead';
        $permission->save();

        //endregion

        //region Campaign

        $permission = new Permission();
        $permission->name = 'campaign_view';
        $permission->display_name = 'View Campaigns';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'campaign_view_all';
        $permission->display_name = 'View All Campaigns';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'campaign_create';
        $permission->display_name = 'Campaign Create';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'campaign_edit';
        $permission->display_name = 'Campaign Edit';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'campaign_delete';
        $permission->display_name = 'Campaign Delete';
        $permission->save();
        //endregion

        //region Email Templates

        $permission = new Permission();
        $permission->name = 'email_template_view';
        $permission->display_name = 'View Email Template';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'email_template_view_all';
        $permission->display_name = 'View All Email Templates';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'email_template_create';
        $permission->display_name = 'Email Template Create';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'email_template_edit';
        $permission->display_name = 'Email Template Edit';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'email_template_delete';
        $permission->display_name = 'Email Template Delete';
        $permission->save();

        //endregion

        //region Campaign Form

        $permission = new Permission();
        $permission->name = 'form_view';
        $permission->display_name = 'View Campaign Forms';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'form_view_all';
        $permission->display_name = 'View All Campaign Forms';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'form_create';
        $permission->display_name = 'Form Campaign Create';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'form_edit';
        $permission->display_name = 'Form Campaign Edit';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'form_delete';
        $permission->display_name = 'Form Campaign Delete';
        $permission->save();

        //endregion


        if(!App::environment('envato'))
        {
            // Manager
            $managerPermissions = Permission::whereIn('name', [
                'import_lead',
                'export_lead',
                'campaign_view',
                'campaign_create',
                'campaign_edit',
                'campaign_delete',
                'email_template_view',
                'email_template_create',
                'email_template_edit',
                'email_template_delete',
                'form_view',
                'form_create',
                'form_edit',
                'form_delete',
            ])->select('id')->get();
            $managerRole = \App\Models\Role::where('name', 'manager')->first();
            foreach ($managerPermissions as $managerPermission)
            {
                $managerRole->insertRolePermission($managerPermission->id);
            }

            // Member
            $memberPermissions = Permission::whereIn('name', [
                'email_template_view',
                'email_template_create',
                'email_template_edit',
                'email_template_delete',
                'form_view',
                'form_create',
                'form_edit',
                'form_delete',
            ])->select('id')->get();
            $role = \App\Models\Role::where('name', 'member')->first();
            foreach ($memberPermissions as $memberPermission)
            {
                $role->insertRolePermission($memberPermission->id);
            }
        }

        \DB::commit();

    }

}
