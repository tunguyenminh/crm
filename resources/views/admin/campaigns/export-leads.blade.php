@extends('admin.admin_layouts')

@section('styles')
    <link href="{{ asset('assets/modules/datatables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/modules/select2/dist/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
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

    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                <select id="searchCampaignType" name="campaign_type" class="form-control select2" onchange="initializeDatatable()">
                    <option value="all">@lang('module_campaign.allCampaigns')</option>
                    <option value="active">@lang('module_campaign.activeCampaigns')</option>
                    <option value="completed">@lang('module_campaign.completedCampaigns')</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="ajax-table" width="100%">
                            <thead>
                                <tr>
                                    <th>@lang('app.id')</th>
                                    <th>@lang('module_campaign.name')</th>
                                    <th>@lang('module_campaign.startedOn')</th>
                                    <th>@lang('module_campaign.totalLeads')</th>
                                    <th>@lang('module_campaign.remainingLeads')</th>
                                    <th>@lang('app.export')</th>
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
<script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js') }}"></script>
<script src="{{ asset('assets/modules/jquery-ui/jquery-ui.min.js') }}"></script>

<script src="{{ asset('assets/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/js/responsive.bootstrap.min.js') }}"></script>
<script>
    var table = $('#ajax-table');

    $(function() {
        initializeDatatable();
    });

    function initializeDatatable() {
        var campaignType = $('#searchCampaignType').val();
        var url = "{{ route('admin.campaigns.get-export-leads') }}?campaign_type=" + campaignType;

        table.dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            bDestroy:true,
            ajax: url,
            "order": [
                [0, "desc"]
            ],
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            language: {
                "url": "@lang('app.datatable')"
            },
            columns: [
                { data: 'id', name: 'campaigns.id'},
                { data: 'name', name: 'campaigns.name'},
                { data: 'started_on', name: 'campaigns.started_on'},
                { data: 'total_leads', name: 'campaigns.total_leads'},
                { data: 'remaining_leads', name: 'campaigns.remaining_leads'},
                { data: 'export', name: 'export', sortable: false}
            ]
        });
    }

    function downloadExportLeadData(id)
    {
        location.href  = "{{ route('admin.campaigns.download-export-leads') }}?campaign_id="+id;
    }

</script>
@endsection