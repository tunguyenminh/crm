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
            <div class="breadcrumb-item">{{ $pageTitle }}</div>
        </div>
    </div>
@endsection

@section('content')

    <div class="row" id="call-enquiry-form">
        <div class="col-md-3">
            <div class="form-group">
                <select id="callHistoryCampaign" class="form-control select2" onchange="callHistoryCampaignSelected()">
                    <option value="all">@lang('module_campaign.selectCampaign')</option>
                    @foreach($user->activeCampaigns() as $allCampaign)
                        <option value="{{ md5($allCampaign->id) }}">{{ $allCampaign->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @if($user->ability('admin', 'campaign_view_all'))
            <div class="col-md-3">
                <div class="form-group">
                    <select id="searchTeamMemberBy" class="form-control select2" onchange="initializeDatatable()">
                        <option value="">@lang('module_lead.selectTeamMember')</option>
                        @foreach($campaignTeamMembers as $campaignTeamMember)
                            <option value="{{ $campaignTeamMember->id }}">{{ $campaignTeamMember->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="users-table" width="100%">
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
    var table = $('#users-table');

    $(function() {
        initializeDatatable();
    });

    function initializeDatatable() {
        var campaign_id = $('#callHistoryCampaign').val();
        var url = '{!! route('admin.get-call-history') !!}?campaign_id='+campaign_id+'&from_page=enquiry';

        @if($user->ability('admin', 'campaign_view_all'))
            var team_member_id = $('#searchTeamMemberBy').val();
            url += '&team_member_id='+team_member_id;
        @endif

        table.dataTable({
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

                    initializeDatatable();
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