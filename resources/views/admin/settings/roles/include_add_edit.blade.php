<div id="accordion">

    <!-- Staff Permission Start !-->
    <div class="accordion">
        <div class="accordion-header" role="button" data-toggle="collapse" data-target="#staff-management-panel">
            <h4>@lang('module_settings.staffManagement')</h4>
        </div>
        <div class="accordion-body collapse show" id="staff-management-panel" data-parent="#accordion">
            <table class="table table-bordered table-role">
                <tbody>
                <tr>
                    <td>@lang('app.add')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['staff_create']['id'] }}" {{ $permission['staff_create']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('app.edit')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['staff_edit']['id'] }}" {{ $permission['staff_edit']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('app.delete')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['staff_delete']['id'] }}" {{ $permission['staff_delete']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('module_settings.assignRoles')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['assign_role']['id'] }}" {{ $permission['assign_role']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Staff Permission End !-->

    <!-- Sales Member Permission Start !-->
    <div class="accordion">
        <div class="accordion-header" role="button" data-toggle="collapse" data-target="#sales-members-management-panel">
            <h4>@lang('module_settings.salesMemberManagement')</h4>
        </div>
        <div class="accordion-body collapse show" id="sales-members-management-panel" data-parent="#accordion">
            <table class="table table-bordered table-role">
                <tbody>
                <tr>
                    <td>@lang('app.add')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['sales_member_create']['id'] }}" {{ $permission['sales_member_create']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('app.edit')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['sales_member_edit']['id'] }}" {{ $permission['sales_member_edit']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('app.delete')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['sales_member_delete']['id'] }}" {{ $permission['sales_member_delete']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Sales Member Permission End !-->

    <!-- Import Lead Permission Start !-->
    <div class="accordion">
        <div class="accordion-header" role="button" data-toggle="collapse" data-target="#import-lead-management-panel">
            <h4>@lang('module_settings.importLeadsManagement')</h4>
        </div>
        <div class="accordion-body collapse show" id="import-lead-management-panel" data-parent="#accordion">
            <table class="table table-bordered table-role">
                <tbody>
                <tr>
                    <td>@lang('module_settings.importLeads')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['import_lead']['id'] }}" {{ $permission['import_lead']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Import Lead Permission End !-->

    <!-- Export Lead Permission Start !-->
    <div class="accordion">
        <div class="accordion-header" role="button" data-toggle="collapse" data-target="#export-lead-management-panel">
            <h4>@lang('module_settings.exportLeadsManagement')</h4>
        </div>
        <div class="accordion-body collapse show" id="export-lead-management-panel" data-parent="#accordion">
            <table class="table table-bordered table-role">
                <tbody>
                <tr>
                    <td>@lang('module_settings.exportLeads')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['export_lead']['id'] }}" {{ $permission['export_lead']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Export Lead Permission End !-->

    <!-- Campaign Permission Start !-->
    <div class="accordion">
        <div class="accordion-header" role="button" data-toggle="collapse" data-target="#campaign-management-panel">
            <h4>@lang('module_settings.campaignManagement')</h4>
        </div>
        <div class="accordion-body collapse show" id="campaign-management-panel" data-parent="#accordion">
            <table class="table table-bordered table-role">
                <tbody>
                <tr>
                    <td>@lang('app.view')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['campaign_view']['id'] }}" {{ $permission['campaign_view']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('module_settings.viewAllCampaigns')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['campaign_view_all']['id'] }}" {{ $permission['campaign_view_all']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('app.add')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['campaign_create']['id'] }}" {{ $permission['campaign_create']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('app.edit')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['campaign_edit']['id'] }}" {{ $permission['campaign_edit']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('app.delete')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['campaign_delete']['id'] }}" {{ $permission['campaign_delete']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Campaign Permission End !-->

    <!-- Email Template Permission Start !-->
    <div class="accordion">
        <div class="accordion-header" role="button" data-toggle="collapse" data-target="#email-template-management-panel">
            <h4>@lang('module_settings.emailTemplateManagement')</h4>
        </div>
        <div class="accordion-body collapse show" id="email-template-management-panel" data-parent="#accordion">
            <table class="table table-bordered table-role">
                <tbody>
                <tr>
                    <td>@lang('app.view')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['email_template_view']['id'] }}" {{ $permission['email_template_view']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('module_settings.viewAllEmailTemplates')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['email_template_view_all']['id'] }}" {{ $permission['email_template_view_all']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('app.add')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['email_template_create']['id'] }}" {{ $permission['email_template_create']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('app.edit')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['email_template_edit']['id'] }}" {{ $permission['email_template_edit']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('app.delete')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['email_template_delete']['id'] }}" {{ $permission['email_template_delete']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Email Template Permission End !-->

    <!-- Campaign Form Permission Start !-->
    <div class="accordion">
        <div class="accordion-header" role="button" data-toggle="collapse" data-target="#campaign-form-management-panel">
            <h4>@lang('module_settings.campaignFormManagement')</h4>
        </div>
        <div class="accordion-body collapse show" id="campaign-form-management-panel" data-parent="#accordion">
            <table class="table table-bordered table-role">
                <tbody>
                <tr>
                    <td>@lang('app.view')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['form_view']['id'] }}" {{ $permission['form_view']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('module_settings.viewAllCampaignForm')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['form_view_all']['id'] }}" {{ $permission['form_view_all']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('app.add')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['form_create']['id'] }}" {{ $permission['form_create']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('app.edit')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['form_edit']['id'] }}" {{ $permission['form_edit']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>@lang('app.delete')</td>
                    <td>
                        <label class="custom-switch">
                            <input type="checkbox" class="custom-switch-input permissions" permissionID="{{ $permission['form_delete']['id'] }}" {{ $permission['form_delete']['status'] }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Campaign Form Permission End !-->

</div>