<?php

// Login And Forget Password Routes
Route::group(['namespace' => 'Auth', 'prefix' => 'admin', 'middleware' => ['web']], function () {

    Route::get('login', ['as' => 'admin.login', 'uses' => 'AdminLoginController@index']);
    Route::post('login', ['as' => 'admin.login_check', 'uses' => 'AdminLoginController@ajaxLogin']);
    Route::get('logout', ['as' => 'admin.logout', 'uses' => 'AdminLoginController@logout']);
});


// Admin Panel After Login
Route::group(['middleware' => ['auth.admin', 'web', 'admin.permission.check'],'namespace' => 'Admin', 'prefix' => 'admin'], function () {

    // region Dashboard Routes
    Route::resource('dashboard', 'DashboardController', ['as' => 'admin']);
    //endregion

    // region Staff Member
    Route::get('get-users', ['as' => 'admin.get-users', 'uses' => 'UserController@getLists']);
    Route::resource('users', 'UserController', ['as' => 'admin']);
    //endregion

    //region Sales Member
    Route::get('get-sales-users', ['as' => 'admin.get-sales-users', 'uses' => 'SalesMemberController@getLists']);
    Route::resource('sales-users', 'SalesMemberController', ['as' => 'admin']);
    //endregion

    // region Email Template
    Route::post('send-mail/{lead}', ['as' => 'admin.email-templates.send-mail', 'uses' => 'EmailTemplateController@sendMail']);
    Route::get('write-edit-email/{lead}/{id?}', ['as' => 'admin.email-templates.write-edit-email', 'uses' => 'EmailTemplateController@writeOrEditEmail']);
    Route::get('get-email-templates', ['as' => 'admin.get-email-templates', 'uses' => 'EmailTemplateController@getLists']);
    Route::resource('email-templates', 'EmailTemplateController', ['as' => 'admin']);

    Route::get('forms/add-new-field', ['as' => 'admin.forms.add-new-field', 'uses' => 'FormController@addNewField']);
    Route::post('forms/upload-fields-from-csv', ['as' => 'admin.forms.upload-fields-from-csv', 'uses' => 'FormController@uploadFieldsFromCSV']);
    Route::post('forms/select-form-data', ['as' => 'admin.forms.select-form-data', 'uses' => 'FormController@selectFormData']);
    Route::get('get-forms', ['as' => 'admin.get-forms', 'uses' => 'FormController@getLists']);
    Route::resource('forms', 'FormController', ['as' => 'admin']);

    //endregion

    //region Campaign Manger
    Route::post('campaigns/save-lead/{campaign_id}', ['as' => 'admin.campaigns.lead.store', 'uses' => 'CampaignController@storeLead']);
    Route::get('campaigns/create-lead/{campaign_id}', ['as' => 'admin.campaigns.lead.create', 'uses' => 'CampaignController@createLead']);
    Route::post('campaigns/save-lead-data', ['as' => 'admin.campaigns.save-lead-data', 'uses' => 'CampaignController@saveLeadData']);
    Route::post('campaigns/import-lead-data', ['as' => 'admin.campaigns.import-lead-data', 'uses' => 'CampaignController@importLeadData']);
    Route::get('campaigns/import-leads', ['as' => 'admin.campaigns.import-leads', 'uses' => 'CampaignController@importLeads']);
    Route::get('campaigns/download-export-leads', ['as' => 'admin.campaigns.download-export-leads', 'uses' => 'CampaignController@downloadExportLead']);
    Route::get('campaigns/get-export-leads', ['as' => 'admin.campaigns.get-export-leads', 'uses' => 'CampaignController@getExportLeadLists']);
    Route::get('campaigns/export-leads', ['as' => 'admin.campaigns.export-leads', 'uses' => 'CampaignController@exportLeadData']);
    Route::get('get-campaigns', ['as' => 'admin.get-campaigns', 'uses' => 'CampaignController@getLists']);
    Route::resource('campaigns', 'CampaignController', ['as' => 'admin']);
    //endregion

    //region Call Manager
    Route::get('callmanager/view-lead/{id}', ['as' => 'admin.callmanager.view-lead', 'uses' => 'CallManagerController@viewLead']);
    Route::post('callmanager/save-lead-time/{id}', ['as' => 'admin.callmanager.save-lead-time', 'uses' => 'CallManagerController@saveLeadTime']);
    Route::post('callmanager/cancel-callback/{id}', ['as' => 'admin.callmanager.cancel-callback', 'uses' => 'CallManagerController@cancelCallback']);
    Route::post('callmanager/skip-delete/{id}', ['as' => 'admin.callmanager.skip-delete', 'uses' => 'CallManagerController@skipAndDelete']);
    Route::post('callmanager/come-back/{id}', ['as' => 'admin.callmanager.come-back', 'uses' => 'CallManagerController@comeBack']);
    Route::post('callmanager/lead-action/{id}/{action}', ['as' => 'admin.callmanager.lead-action', 'uses' => 'CallManagerController@takeLeadAction']);
    Route::post('callmanager/save-lead/{id}', ['as' => 'admin.callmanager.save-lead', 'uses' => 'CallManagerController@saveLeadData']);
    Route::post('callmanager/stop/{id}', ['as' => 'admin.callmanager.stop', 'uses' => 'CallManagerController@stopCampaign']);
    Route::post('callmanager/take-action/{campaign}', ['as' => 'admin.callmanager.take-action', 'uses' => 'CallManagerController@takeAction']);
    Route::get('callmanager/{lead}', ['as' => 'admin.callmanager.lead', 'uses' => 'CallManagerController@startLead']);
    Route::get('get-call-manager', ['as' => 'admin.get-call-manager', 'uses' => 'CallManagerController@getLists']);
    Route::get('callmanager', ['as' => 'admin.callmanager.index', 'uses' => 'CallManagerController@index']);
    //endregion

    //region Call Enquiry
    Route::post('call-enquiry/campaign-form-field/{id}', ['as' => 'admin.call-enquiry.campaign-form-field', 'uses' => 'CallEnquiryController@getFormFieldsByCampaign']);
    Route::get('get-call-enquiry', ['as' => 'admin.get-call-enquiry', 'uses' => 'CallEnquiryController@getLists']);
    Route::resource('call-enquiry', 'CallEnquiryController', ['as' => 'admin', 'only' => ['index']]);
    //endregion

    //region Call History
    Route::post('call-history/campaign-team-members/{id}', ['as' => 'admin.call-history.campaign-team-member', 'uses' => 'CallHistoryController@getCampaignTeamMember']);
    Route::get('get-call-history', ['as' => 'admin.get-call-history', 'uses' => 'CallHistoryController@getLists']);
    Route::resource('call-history', 'CallHistoryController', ['as' => 'admin', 'only' => ['index']]);
    //endregion

    //region Appointment

    // Pending Callbacks
    Route::get('add-edit-callback/{id}', ['as' => 'admin.add-edit-callback', 'uses' => 'PendingCallbackController@addEditByLead']);
    Route::get('get-callbacks', ['as' => 'admin.get-callbacks', 'uses' => 'PendingCallbackController@getLists']);
    Route::resource('pending-callback', 'PendingCallbackController', ['as' => 'admin']);

    // Appointment Calendar
    Route::get('add-edit-appointments/{id}', ['as' => 'admin.add-edit-appointments', 'uses' => 'AppointmentCalendarController@addEditByLead']);
    Route::post('get-appointments', ['as' => 'admin.get-appointments', 'uses' => 'AppointmentCalendarController@getAppointments']);
    Route::resource('appointments', 'AppointmentCalendarController', ['as' => 'admin']);

    //endregion

    //region Settings Routes
    Route::resource('settings/profile', 'ProfileSettingController', ['as' => 'admin.settings', 'only' => ['index','store']]);
    Route::group(
        ['prefix' => 'settings', 'middleware' => ['auth.admin.check']], function () {

        Route::resource('company', 'CompanySettingController', ['as' => 'admin.settings', 'only' => ['index','store']]);
        Route::post('send-test-email', ['as' => 'admin.settings.send-test-email', 'uses' => 'EmailSettingController@sendTestEmail']);
        Route::get('get-send-mail-modal', ['as' => 'admin.settings.get-send-mail-modal', 'uses' => 'EmailSettingController@getSendMailModal']);
        Route::resource('email', 'EmailSettingController', ['as' => 'admin.settings', 'only' => ['index','store']]);

        Route::get('translations/view/{groupKey?}', ['as' => 'admin.settings.translations.get-view', 'uses' => 'TranslationController@getView'])->where('groupKey', '.*');
        Route::get('translations/{groupKey?}', ['as' => 'admin.settings.translations', 'uses' => 'TranslationController@getIndex'])->where('groupKey', '.*');

        Route::post('calls/save-twilio-number', ['as' => 'admin.settings.calls.save-twilio-number', 'uses' => 'CallSettingController@saveTwilioNumber']);
        Route::resource('calls', 'CallSettingController', ['as' => 'admin.settings', 'only' => ['index','store']]);

        Route::resource('form-field-name', 'FormFieldNameSettingController', ['as' => 'admin.settings', 'only' => ['index','store']]);

        //region Role Routes
        Route::get('get-roles', ['as' => 'admin.get-roles', 'uses' => 'RoleSettingController@getList']);
        Route::resource('roles', 'RoleSettingController', ['as' => 'admin.settings']);
        //endregion

        //region Update App Routes
        Route::post('install-by-file', ['as' => 'admin.settings.update-app.install-by-file', 'uses' => 'UpdateAppSettingController@installByFile']);
        Route::post('delete-file', ['as' => 'admin.settings.update-app.delete-file', 'uses' => 'UpdateAppSettingController@deleteFile']);
        Route::resource('update-app', 'UpdateAppSettingController', ['as' => 'admin.settings']);
        //endregion

    });
    // endregion

});


Route::post('/twilio/inbound-webhook/{number}', ['as' => 'front.twilio.inbound-webhook', 'uses' => 'Front\TwilioCallController@inboundWebhookHandler']);
Route::post('/twilio/token', ['as' => 'front.twilio.token', 'uses' => 'Front\TwilioCallController@newToken']);
Route::post('/twilio/support/call', ['as' => 'front.twilio.support-call', 'uses' => 'Front\TwilioCallController@newCall']);

Route::get('/', ['as' => 'admin.app', 'uses' => 'Auth\AdminLoginController@index']);