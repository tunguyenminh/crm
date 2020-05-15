@extends('admin.admin_layouts')

@section('styles')
    <link href="{{ asset('assets/modules/datatables/datatables.min.css') }}" rel="stylesheet">
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

    <div class="card mb-0">
        <div class="card-header">
            <h4> @if($type == 'active') @lang('module_campaign.activeCampaigns') @else  @lang('module_campaign.completedCampaigns') @endif </h4>
            <div class="card-header-action">
                <div class="btn-group">
                    <a href="{{ route('admin.callmanager.index') }}" class="btn @if($type == 'active') btn-primary @endif">@lang('module_campaign.activeCampaigns')</a>
                    <a href="{{ route('admin.callmanager.index') }}?type=completed" class="btn @if($type == 'completed') btn-primary @endif">@lang('module_campaign.completedCampaigns')</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="campaigns_lists">
        @if($type == 'active')
            @forelse($userCampaigns as $userCampaign)
                <div class="col-12 col-md-12 col-lg-4">
                <div class="card card-success profile-widget">
                    <div class="card-header">
                        <h4><a href="{{ route('admin.campaigns.show', [md5($userCampaign->id)]) }}"> {{ $userCampaign->name }}</a></h4>
                    </div>
                    <div class="profile-widget-header">
                        <div class="profile-widget-items">
                            <div class="profile-widget-item">
                                <div class="profile-widget-item-label">@lang('app.remaining')</div>
                                <div class="profile-widget-item-value">{{ $userCampaign->remaining_leads ?? '-' }}</div>
                            </div>
                            <div class="profile-widget-item">
                                <div class="profile-widget-item-label">@lang('app.completed')</div>
                                <div class="profile-widget-item-value">{{ $userCampaign->total_leads -  $userCampaign->remaining_leads }}</div>
                            </div>
                            <div class="profile-widget-item">
                                <div class="profile-widget-item-label">@lang('app.total')</div>
                                <div class="profile-widget-item-value">{{ $userCampaign->total_leads ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">@lang('module_campaign.startedOn')</div>
                            <div class="col-md-8">
                                <strong>{{ $userCampaign->started_on != NULL ? $userCampaign->started_on->format('d F, Y') : '-' }}</strong>
                            </div>
                        </div>
                        <div class="row pt-4">
                            <div class="col-md-4">@lang('module_campaign.members')</div>
                            <div class="col-md-8">
                                @foreach($userCampaign->staffMembers as $staffMember)
                                    @if($staffMember->user->last_name)
                                        @php($shortName = ucfirst($staffMember->user->first_name[0]))
                                    @else
                                        @php($shortName = ucfirst($staffMember->user->first_name[0]).ucfirst($staffMember->user->last_name[0]))
                                    @endif
                                    <figure class="avatar mr-2 mb-2 avatar-sm bg-success text-white" data-initial="{{ $shortName }}" data-toggle="tooltip" title="{{ $staffMember->user->name }}"></figure>
                                @endforeach
                            </div>
                        </div>
                        <div class="row pt-4">
                            <div class="col-md-4">@lang('module_campaign.progress')</div>
                            <div class="col-md-8">
                                @if($userCampaign->remaining_leads === 0)
                                    @lang('module_campaign.flyCampaign')
                                @elseif($userCampaign->total_leads != null && $userCampaign->remaining_leads != null)
                                    @php($percentage = intval((($userCampaign->total_leads - $userCampaign->remaining_leads)/$userCampaign->total_leads)*100))
                                    <div class="progress" data-height="6" data-toggle="tooltip" title="{{ $percentage }}%">
                                        <div class="progress-bar bg-success" data-width="{{$percentage}}%"></div>
                                    </div>
                                    <div class="pt-2">
                                        @lang('module_campaign.remainingLeads'): <strong>{{ ($userCampaign->total_leads - $userCampaign->remaining_leads).'/'.$userCampaign->total_leads }}</strong>
                                    </div>
                                @else
                                    @lang('module_campaign.notStarted')
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="card-footer pt-3 d-flex justify-content-center footer-border">
                        <div class="budget-price justify-content-center">
                            @if($userCampaign->remaining_leads == null && $userCampaign->total_leads == null)
                                <a href="javascript:void(0);" onclick="takeCampaign('{{md5($userCampaign->id)}}', 'start_new')"  class="budget-price-label text-primary"><i class="fa fa-play"></i> @lang('module_campaign.startAndNewLead')</a>
                            @elseif($userCampaign->remaining_leads === 0)
                                <a href="javascript:void(0);" onclick="takeCampaign('{{md5($userCampaign->id)}}', 'new')"  class="budget-price-label text-warning"><i class="fa fa-play"></i> @lang('module_campaign.newLead')</a>
                            @elseif($userCampaign->remaining_leads == $userCampaign->total_leads)
                                <a href="javascript:void(0);" onclick="takeCampaign('{{md5($userCampaign->id)}}', 'start')"  class="budget-price-label text-info"><i class="fa fa-play"></i> @lang('app.start')</a>
                            @else
                                <a href="javascript:void(0);" onclick="takeCampaign('{{md5($userCampaign->id)}}', 'resume')" class="budget-price-label text-success"><i class="fa fa-sync"></i> @lang('app.resume')</a>
                            @endif
                        </div>
                        <div class="budget-price justify-content-center">
                            <a href="javascript:void(0);" onclick="stopCampaign({{$userCampaign->id}})" class="budget-price-label text-danger"><i class="fa fa-stop"></i> @lang('app.stop')</a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
                <div class="col-md-12 pt-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="empty-state" data-height="400">
                                <div class="empty-state-icon">
                                    <i class="fas fa-question"></i>
                                </div>
                                <h2>@lang('module_campaign.noCampaignAssigned')</h2>
                                <p class="lead">
                                    @lang('module_campaign.noCampaignAssignedMessage')
                                </p>
                                @if($user->ability('admin', 'campaign_create'))
                                    <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary mt-4"><i class="fa fa-plus"></i> @lang('module_campaign.addNewCampaign') </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        @else
                <div class="col-md-12 pt-4">
                    <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="users-table" width="100%">
                                <thead>
                                <tr>
                                    <th>@lang('app.id')</th>
                                    <th>@lang('module_campaign.name')</th>
                                    <th>@lang('module_campaign.totalLeads')</th>
                                    <th>@lang('module_campaign.startedOn')</th>
                                    <th>@lang('module_campaign.completedOn')</th>
                                    <th>@lang('app.action')</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    </div>

            </div>
        @endif
    </div>
@endsection

@section('modals')
    @include('admin.includes.add-edit-modal')
@endsection

@section('scripts')
<script src="{{ asset('assets/modules/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>

<script src="{{ asset('assets/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/js/responsive.bootstrap.min.js') }}"></script>
<script>

    @if($type == 'active')

    function takeCampaign(id, action) {
        var actionText = "{{ trans('module_campaign.startCampaignLead') }}";

        if(action === 'new')
        {
            actionText = "{!! trans('module_campaign.newCampaignLeadCreate') !!}";
        } else if(action === 'start_new'){
            actionText = "{!! trans('module_campaign.startAndCreateLead') !!}";
        } else if(action === 'resume'){
            actionText = "{!! trans('module_campaign.resumeCampaignLead') !!}";
        }

        swal({
            title: "{{ trans('app.areYouSure') }}",
            text: actionText,
            dangerMode: true,
            icon: 'warning',
            buttons: {
                cancel: "{{ trans('app.no') }}",
                confirm: {
                    text: "{{ trans('app.yes') }}",
                    value: true,
                    visible: true,
                    className: "danger",
                }
            },
        }).then(function(isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.callmanager.take-action', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token},
                    container: "#campaigns_lists"
                });
            }
        });
    };

    function stopCampaign(id) {
        swal({
            title: "{{ trans('app.areYouSure') }}",
            text: "{{ trans('module_campaign.stopCampaignMessage') }}",
            dangerMode: true,
            icon: 'warning',
            buttons: {
                cancel: "{{ trans('app.no') }}",
                confirm: {
                    text: "{{ trans('app.yes') }}",
                    value: true,
                    visible: true,
                    className: "danger",
                }
            },
        }).then(function(isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.callmanager.stop', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token},
                    container: "#campaigns_lists",
                });
            }
        });
    };

    @else

    var table = $('#users-table');

    $(function() {
        table.dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{!! route('admin.get-call-manager') !!}',
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
                { data: 'total_leads', name: 'campaigns.total_leads'},
                { data: 'started_on', name: 'campaigns.started_on'},
                { data: 'completed_on', name: 'campaigns.completed_on'},
                { data: 'action', name: 'action'}
            ]
        });

    });

    @endif
</script>
@endsection