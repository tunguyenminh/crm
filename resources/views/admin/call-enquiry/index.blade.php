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
                <select id="callEnquiryCampaign" class="form-control select2" onchange="callEnquiryCampaignSelected()">
                    <option value="all">@lang('module_campaign.selectCampaign')</option>
                    @foreach($user->activeCampaigns() as $allCampaign)
                        <option value="{{ $allCampaign->id }}">{{ $allCampaign->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <select id="searchFieldBy" class="form-control select2">
                    <option value="">@lang('module_call_enquiry.searchBy')</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <input type="text" id="searchFieldValue" class="form-control" placeholder="@lang('module_campaign.enterSearchTerm')">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <a href="javascript:void(0);" onclick="initializeDatatable()" class="btn btn-icon btn-block icon-left btn-primary"><i class="fa fa-search"></i> @lang('app.search') </a>
            </div>
        </div>
    </div>

    <div class="row" id="campaignStats">
        @include('admin.call-enquiry.call-enquiry-stats')
    </div>

    <div class="row">
        <div class="col-12">
            {{--<div class="card">--}}
                {{--<div class="card-body">--}}

                    {{--<div class="form-row mt-4 mb-4">--}}
                        {{--<div class="col-md-10">--}}
                            {{--<div class="row">--}}
                                {{--<label for="reference_number" class="col-sm-3 text-right col-form-label">@lang('app.campaign')</label>--}}
                                {{--<div class="col-sm-9">--}}
                                    {{--<select id="inputState" class="form-control">--}}
                                        {{--@foreach($allCampaigns as $allCampaign)--}}
                                            {{--<option value="interested">{{ $allCampaign->name }}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    {{--<div class="form-row mb-4">--}}
                        {{--<div class="col-md-10">--}}
                            {{--<div class="row">--}}
                                {{--<label for="reference_number" class="col-sm-3 text-right col-form-label">@lang('app.campaign')</label>--}}
                                {{--<div class="col-sm-4">--}}
                                    {{--<select id="inputState" class="form-control">--}}
                                        {{--@foreach($allCampaigns as $allCampaign)--}}
                                            {{--<option value="interested">{{ $allCampaign->name }}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                                {{--<div class="col-sm-5">--}}
                                    {{--<input type="text" class="form-control">--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    {{--<div class="form-row">--}}
                        {{--<div class="col-md-10">--}}
                            {{--<div class="row">--}}
                                {{--<label for="reference_number" class="col-sm-3 text-right col-form-label"></label>--}}
                                {{--<div class="col-sm-4">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<a href="javascript:void(0);" onclick="addModal()" class="btn btn-icon icon-left btn-primary"><i class="fa fa-plus"></i> @lang('module_user.addNewUser') </a>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                {{--</div>--}}
            {{--</div>--}}

            <div class="card">

                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-striped" id="users-table" width="100%">
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
        var campaign_id = $('#callEnquiryCampaign').val();
        var form_field_id = $('#searchFieldBy').val();
        var form_field_value = $('#searchFieldValue').val();

        table.dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            bDestroy:true,
            ajax: '{!! route('admin.get-call-enquiry') !!}?campaign_id='+campaign_id+'&form_field_id='+form_field_id+'&form_field_value='+form_field_value+'&from_page=enquiry',
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
        var id = $('#callEnquiryCampaign').val();

        var url = "{{ route('admin.call-enquiry.campaign-form-field',':id') }}";
        url = url.replace(':id', id);

        var token = "{{ csrf_token() }}";

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'_token': token},
            container: "#call-enquiry-form",
            success: function (response) {
                if (response.status == "success") {
                    $('#searchFieldBy').html(response.data.html);
                    $('#campaignStats').html(response.data.stats);
                    $('#searchFieldValue').val('');

                    initializeDatatable();
                }
            }
        });
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
                var campaign_id = $('#callEnquiryCampaign').val();
                var token = "{{ csrf_token() }}";

                var url = "{{ route('admin.callmanager.skip-delete', ':id') }}?delete=yes&campaign_id="+campaign_id;
                url = url.replace(':id', id);

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token},
                    success: function (response) {
                        if (response.status == "success") {
                            $('#campaignStats').html(response.data.stats);
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
</script>
@endsection