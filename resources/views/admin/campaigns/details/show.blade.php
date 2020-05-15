@extends('admin.admin_layouts')

@section('styles')
    <link href="{{ asset('assets/modules/datatables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/modules/select2/dist/css/select2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
@endsection

@section('breadcrumb')

    <div class="section-header">
        <h1><i class="{{ $pageIcon }}"></i> {{ $pageTitle }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">@lang('menu.home')</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.campaigns.index') }}">@lang('menu.campaigns')</a></div>
            <div class="breadcrumb-item">{{ $pageTitle }}</div>
        </div>
    </div>
@endsection

@section('content')

    <div class="row mt-2">
        <div class="col-md-3">
            <div class="form-group">
                <select id="callSelectedCampaign" class="form-control select2" onchange="callEnquiryCampaignSelected()">
                    @if($campaignDetails->status == 'completed')
                        <option value="{{ md5($campaignDetails->id) }}" selected>{{ $campaignDetails->name }}</option>
                    @endif
                    @foreach($user->activeCampaigns() as $allCampaign)
                        <option value="{{ md5($allCampaign->id) }}" @if($allCampaign->id == $campaignDetails->id) selected @endif>{{ $allCampaign->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card card-statistic-2">
                <div class="card-icon shadow-primary bg-primary">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>@lang('module_campaign.totalLeads')</h4>
                    </div>
                    <div class="card-body">
                       {{ $campaignDetails->total_leads }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card card-statistic-2">
                <div class="card-icon shadow-primary bg-primary">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>@lang('module_campaign.completedLeads')</h4>
                    </div>
                    <div class="card-body">
                        {{ $campaignDetails->total_leads - $campaignDetails->remaining_leads }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card card-statistic-2">
                <div class="card-icon shadow-primary bg-primary">
                    <i class="fas fa-headphones"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        @if($campaignDetails->status == 'completed')
                            <h4>@lang('module_campaign.yourLeads')</h4>
                        @else
                            <h4>@lang('module_campaign.remainingLeads')</h4>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($campaignDetails->status == 'completed')
                            {{ $yourLeadCount }}
                        @else
                            {{ $campaignDetails->remaining_leads }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs custom-tab" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="campaign-leads-tab" data-toggle="tab" href="#campaign-leads" role="tab" aria-controls="campaign-leads" aria-selected="true"><i class="fa fa-address-book"></i> @lang('module_lead.leads')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="call-history-tab" data-toggle="tab" href="#performance" role="tab" aria-controls="performance" aria-selected="false"><i class="fa fa-chart-pie"></i> @lang('module_campaign.performance')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="call-history-tab" data-toggle="tab" href="#call-history" role="tab" aria-controls="call-history" aria-selected="false"><i class="fa fa-stopwatch"></i> @lang('menu.callHistory')</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="campaign-leads" role="tabpanel" aria-labelledby="campaign-leads-tab">

                    <div class="row mt-3">
                        <div class="col-md-3">
                        @if(($user->ability('admin', 'campaign_view_all') || $campaignDetails->created_by == $user->id) && $campaignDetails->status != 'completed')
                            <div class="form-group">
                                <a href="javascript:void(0);" onclick="addLeadModal()"  class="btn btn-icon icon-left btn-primary"><i class="fa fa-plus"></i> @lang('module_lead.addNewLead') </a>
                            </div>
                        @else
                                <div class="form-group">
                                   &nbsp;
                                </div>
                        @endif
                        </div>
                        <div class="col-md-9">
                            <form class="form-inline pull-right" id="call-enquiry-form">
                                <div class="form-group ml-2">
                                    <select id="searchFieldBy" class="form-control select2" style="min-width: 170px;">
                                        <option value="">@lang('module_call_enquiry.searchBy')</option>
                                        @foreach($formFields as $formField)
                                            <option value="{{ $formField->id }}">{{ $formField->field_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group ml-2">
                                    <input type="text" id="searchFieldValue" class="form-control" placeholder="@lang('module_campaign.enterSearchTerm')">
                                </div>
                                <div class="form-group ml-2">
                                    <a href="javascript:void(0);" onclick="initializeDatatable()" class="btn btn-icon btn-block icon-left btn-primary"><i class="fa fa-search"></i> @lang('app.search') </a>
                                </div>
                            </form>
                        </div>

                    </div>

                    <div class="card">

                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="table table-striped" id="campaign-leads-table" width="100%">
                                    <thead>
                                    <tr>
                                        <th>@lang('app.id')</th>
                                        <th>@lang('module_lead.referenceNumber')</th>
                                        <th>@lang('module_call_enquiry.contactPerson')</th>
                                        <th>@lang('module_user.email')</th>
                                        <th>@lang('app.campaign')</th>
                                        <th>@lang('module_call_enquiry.callingAgent')</th>
                                        <th>@lang('app.action')</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="performance" role="tabpanel" aria-labelledby="performance-tab">
                    <div class="row mt-4">
                        <div class="col-12 col-sm-12 col-lg-12">
                            @if($isTopper && $campaignDetails->total_leads != 0)
                                <div class="alert alert-success alert-has-icon">
                                    <div class="alert-icon"><i class="far fa-thumbs-up"></i></div>
                                    <div class="alert-body">
                                        <div class="alert-title">@lang('app.congratulation')</div>
                                        @lang('module_campaign.campaignTopperMessage')
                                    </div>
                                </div>
                            @endif
                            <div class="card">
                                <div class="card-header">
                                    <h4>@lang('module_campaign.staffMemberPerformance')</h4>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled user-progress list-unstyled-border list-unstyled-noborder">
                                        @foreach($campaignDetails->staffMembers->sortBy('total_leads', SORT_REGULAR, true) as $campaignStaffMember)
                                            @if($campaignDetails->total_leads == 0)
                                                @php($userLeadProgress=0)
                                            @else
                                                @php($userLeadProgress=($campaignStaffMember->total_leads/$campaignDetails->total_leads)*100)
                                            @endif
                                            <li class="media pt-15">
                                                <img alt="{{ $campaignStaffMember->user->name }}" class="mr-3 rounded-circle" width="50" src="{{ $campaignStaffMember->user->image_url }}">
                                                <div class="media-body">
                                                    <div class="media-title">{{ $campaignStaffMember->user->name }}</div>
                                                    <div class="text-job text-muted">Total Call Made: {{ $campaignStaffMember->total_leads }}</div>
                                                </div>
                                                <div class="media-progressbar">
                                                    <div class="progress-text">{{ (int) $userLeadProgress }}%</div>
                                                    <div class="progress" data-height="6">
                                                        <div class="progress-bar bg-primary" data-width="{{ (int) $userLeadProgress }}%"></div>
                                                    </div>
                                                </div>
                                                <div class="media-cta">
                                                    <div class="media-items">
                                                        <div class="media-item">
                                                            <div class="media-value">{{ $campaignStaffMember->interested_leads }}</div>
                                                            <div class="media-label">@lang('module_lead.interested')</div>
                                                        </div>
                                                        <div class="media-item" style="flex: none;">
                                                            <div class="media-value">{{ $campaignStaffMember->not_interested_leads }}</div>
                                                            <div class="media-label">@lang('module_lead.notInterested')</div>
                                                        </div>
                                                        <div class="media-item">
                                                            <div class="media-value">{{ $campaignStaffMember->unreachable_leads }}</div>
                                                            <div class="media-label">@lang('module_lead.unreachable')</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="call-history" role="tabpanel" aria-labelledby="call-history-tab">
                    @if($user->ability('admin', 'campaign_view_all') || $campaignDetails->created_by == $user->id)
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <select id="searchTeamMemberBy" class="form-control select2" onchange="initializeCallHistoryDatatable()" style="width: 100%">
                                    <option value="">@lang('module_lead.selectTeamMember')</option>
                                    @foreach($campaignTeamMembers as $campaignTeamMember)
                                        <option value="{{ $campaignTeamMember->id }}">{{ $campaignTeamMember->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="call-history-table" width="100%">
                                    <thead>
                                    <tr>
                                        <th>@lang('app.id')</th>
                                        <th>@lang('module_lead.referenceNumber')</th>
                                        <th>@lang('module_lead.leadDetails')</th>
                                        <th>@lang('module_lead.callDetails')</th>
                                        <th>@lang('app.campaign')</th>
                                        <th>@lang('module_call_enquiry.lastCallingAgent')</th>
                                        <th>@lang('app.action')</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('modals')
    @include('admin.includes.add-edit-modal')
@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>

    <script src="{{ asset('assets/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/js/responsive.bootstrap.min.js') }}"></script>

    <script>
        var table = $('#campaign-leads-table');

        $(function() {
            initializeDatatable();
        });

        function initializeDatatable() {
            var form_field_id = $('#searchFieldBy').val();
            var form_field_value = $('#searchFieldValue').val();

            table.dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                bDestroy:true,
                ajax: '{!! route('admin.get-call-enquiry') !!}?campaign_id={{ $campaignDetails->id }}&form_field_id='+form_field_id+'&form_field_value='+form_field_value+'&from_page=campaign_detail',
                aaSorting: [[0, "desc"]],
                language: {
                    "url": "@lang('app.datatable')"
                },
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
                    { data: 'lead_id', name: 'lead_id'},
                    { data: 'reference_number', name: 'leads.reference_number'},
                    { data: 'contact_person', name: 'contact_person', sortable: false},
                    { data: 'email', name: 'email', sortable: false},
                    { data: 'campaign_name', name: 'campaigns.name'},
                    { data: 'calling_agent', name: 'users.first_name'},
                    { data: 'action', name: 'action'}
                ]
            });
        }

        function callEnquiryCampaignSelected() {
            var campaignId = $('#callSelectedCampaign').val();

            window.location.href = "{{ route('admin.campaigns.show', ':id') }}".replace(':id', campaignId);
        }

        function deleteLead(id) {
            swal({
                title: "{{ trans('module_call_enquiry.deleteLead') }}?",
                text: "{{ trans('module_call_enquiry.deleteTextMessage') }}",
                dangerMode: true,
                icon: 'warning',
                buttons: {
                    cancel: "{{ trans('app.noCancelIt') }}",
                    confirm: {
                        text: "{{ trans('app.yesDeleteIt') }}",
                        value: true,
                        visible: true,
                        className: "danger"
                    }
                }
            }).then(function(isConfirm) {

                if (isConfirm)
                {
                    var token = "{{ csrf_token() }}";

                    var url = "{{ route('admin.callmanager.skip-delete', ':id') }}?delete=yes&campaign_id={{$campaignDetails->id}}";
                    url = url.replace(':id', id);

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token},
                        success: function (response) {
                            if (response.status == "success") {
                                table._fnDraw();
                            }
                        }
                    });
                }

            });
        }

        function viewLead (id) {
            var url = '{{ route('admin.callmanager.view-lead', ':id') }}';
            url      = url.replace(':id',id);
            $.ajaxModal('#addEditModal', url)
        }

        @if($user->ability('admin', 'campaign_view_all') || $campaignDetails->created_by == $user->id)
            function addLeadModal () {
                $.ajaxModal('#addEditModal','{{ route('admin.campaigns.lead.create', [md5($campaignDetails->id)]) }}');
            }

            function addNewLead(id) {
                var url = "{{ route('admin.campaigns.lead.store', [':id']) }}";
                url = url.replace(':id', id);

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    file: true,
                    container: "#lead-add-edit-form",
                    messagePosition: "toastr",
                    success: function(response) {
                        if (response.status == "success") {
                            $('#addEditModal').modal('hide');
                            table._fnDraw();
                        }
                    }
                });
            }

        @endif
    </script>

    <script>
        var callHistoryTable = $('#call-history-table');

        $(function() {
            initializeCallHistoryDatatable();
        });

        function initializeCallHistoryDatatable() {
            var campaign_id = $('#callHistoryCampaign').val();
            var url = '{!! route('admin.get-call-history') !!}?campaign_id={{ $campaignDetails->id }}&from_page=campaign_detail';

            @if($user->ability('admin', 'campaign_view_all'))
            var team_member_id = $('#searchTeamMemberBy').val();
            url += '&team_member_id='+team_member_id;
            @endif

            callHistoryTable.dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                bDestroy:true,
                ajax: url,
                aaSorting: [[0, "desc"]],
                language: {
                    "url": "@lang('app.datatable')"
                },
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
                    { data: 'lead_id', name: 'lead_id'},
                    { data: 'reference_number', name: 'leads.reference_number'},
                    { data: 'contact_person', name: 'contact_person', sortable: false},
                    { data: 'time_taken', name: 'time_taken', sortable: false},
                    { data: 'campaign_name', name: 'campaigns.name'},
                    { data: 'calling_agent', name: 'users.first_name'},
                    { data: 'action', name: 'action'}
                ]
            });
        }

        function callHistoryCampaignSelected() {
            var id = $('#callHistoryCampaign').val();

            var url = "{{ route('admin.call-history.campaign-team-member',':id') }}";
            url = url.replace(':id', id);

            var token = "{{ csrf_token() }}";

            $.easyAjax({
                type: 'POST',
                url: url,
                data: {'_token': token},
                container: "#call-enquiry-form",
                success: function (response) {
                    if (response.status == "success") {
                        $('#searchTeamMemberBy').html(response.data.html);

                        initializeCallHistoryDatatable();
                    }
                }
            });
        }

        function viewLead (id) {
            var url = '{{ route('admin.callmanager.view-lead', ':id') }}';
            url      = url.replace(':id',id);
            $.ajaxModal('#addEditModal', url)
        }
    </script>
@endsection