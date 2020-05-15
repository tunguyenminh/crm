@extends('admin.admin_layouts')

@section('styles')
    <link href="{{ asset('assets/modules/datatables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet">
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

    @if($user->ability('admin', 'campaign_create'))
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <a href="{{ route('admin.campaigns.create') }}"  class="btn btn-icon icon-left btn-primary"><i class="fa fa-plus"></i> @lang('module_campaign.addNewCampaign') </a>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>@if($campaignType == 'active') @lang('module_campaign.activeCampaigns') @else  @lang('module_campaign.completedCampaigns') @endif</h4>
                    <div class="card-header-action">
                        <div class="btn-group">
                            <a href="{{ route('admin.campaigns.index') }}?type=active" class="btn @if($campaignType == 'active') btn-primary @endif">@lang('module_campaign.activeCampaigns')</a>
                            <a href="{{ route('admin.campaigns.index') }}?type=completed" class="btn @if($campaignType == 'completed') btn-primary @endif">@lang('module_campaign.completedCampaigns')</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="users-table" width="100%">
                            <thead>
                                <tr>
                                    <th>@lang('app.id')</th>
                                    <th>@lang('module_campaign.name')</th>
                                    @if($campaignType == 'active')
                                        <th>@lang('module_campaign.progress')</th>
                                        <th>@lang('module_campaign.campaignMembers')</th>
                                        <th>@lang('module_campaign.startedOn')</th>
                                        <th>@lang('module_campaign.lastActiveMember')</th>
                                    @else
                                        <th>@lang('module_campaign.totalLeads')</th>
                                        <th>@lang('module_campaign.startedOn')</th>
                                        <th>@lang('module_campaign.completedOn')</th>
                                    @endif
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
<script src="{{ asset('assets/modules/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.js') }}"></script>

<script src="{{ asset('assets/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/js/responsive.bootstrap.min.js') }}"></script>
<script>
    var table = $('#users-table');

    $(function() {
        table.dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{!! route('admin.get-campaigns') !!}?campaign_type={{ $campaignType }}',
            aaSorting: [[0, "desc"]],
            language: {
                "url": "@lang('app.datatable')"
            },
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });

                $('[data-height]').each(function() {
                    $(this).css({
                        height: $(this).data('height')
                    });
                });

                $('[data-width]').each(function() {
                    $(this).css({
                        width: $(this).data('width')
                    });
                });
            },
            columns: [
                { data: 'id', name: 'campaigns.id'},
                { data: 'name', name: 'campaigns.name'},
                @if($campaignType == 'active')
                { data: 'progress', name: 'progress', sortable: false},
                { data: 'members', name: 'members', sortable: false},
                { data: 'started_on', name: 'campaigns.started_on'},
                { data: 'last_active_member', name: 'last_active_member', sortable: false, searchable: false},
                @else
                { data: 'total_leads', name: 'campaigns.total_leads'},
                { data: 'started_on', name: 'campaigns.started_on'},
                { data: 'completed_on', name: 'campaigns.completed_on'},
                @endif
                { data: 'action', name: 'action'}
            ]
        });

    });

    function deleteCampaignModal(id) {
            swal({
                title: "{{ trans('app.areYouSure') }}",
                text: "{{ trans('app.areYouSure') }}",
                dangerMode: true,
                icon: 'warning',
                buttons: {
                    cancel: "{{ trans('app.noCancelIt') }}",
                    confirm: {
                        text: "{{ trans('app.yesDeleteIt') }}",
                        value: true,
                        visible: true,
                        className: "danger",
                    }
                },
            }).then(function(isConfirm) {
                if (isConfirm) {

                    var url = "{{ route('admin.campaigns.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'DELETE',
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
        };

    @if($user->ability('admin', 'campaign_edit'))
    function editCampaignModal (id) {
        var url = '{{ route('admin.campaigns.edit', ':id') }}';
        url      = url.replace(':id',id);
        $.ajaxModal('#addEditModal', url)
    }

    function editCampaign(id) {

        var url = "{{route('admin.campaigns.update',':id')}}";
        url = url.replace(':id', id);

        $.easyAjax({
            type: 'POST',
            url: url,
            container: "#campaigns-edit-form",
            data: $('#campaigns-edit-form').serialize(),
            messagePosition: "toastr",
            success: function (response) {
                if (response.status == "success") {
                    $('#addEditModal').modal('hide');
                    table._fnDraw();
                }
            }
        });

    }
    @endif

    @if($user->ability('admin', 'campaign_view_all') || $campaignDetails->created_by == $user->id)
    function addLeadModal (id) {
        var url = "{{ route('admin.campaigns.lead.create', [':id']) }}";
        url = url.replace(':id', id);

        $.ajaxModal('#addEditModal', url);
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
@endsection