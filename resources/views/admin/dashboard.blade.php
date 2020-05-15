@extends('admin.admin_layouts')

@section('breadcrumb')

    <div class="section-header">
        <h1>{{ $pageTitle }}</h1>
    </div>
@endsection

@section('content')

    @include('admin.includes.update_info')

    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card card-statistic-2">
                <div class="card-icon shadow-primary bg-primary">
                    <i class="fas fa-business-time"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>@lang('module_call_enquiry.yourCampaigns')</h4>
                    </div>
                    <div class="card-body">
                        {{ $yourCampaigns }}
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
                        <h4>@lang('module_call_enquiry.callMade')</h4>
                    </div>
                    <div class="card-body">
                        {{ $yourLeads }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card card-statistic-2">
                <div class="card-icon shadow-primary bg-primary">
                    <i class="fas fa-stopwatch"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>@lang('module_call_enquiry.totalDuration')</h4>
                    </div>
                    <div class="card-body">
                        <p style="font-size: 12px; font-weight: 700;">{{ $totalTimes > 0 ? \App\Classes\Common::secondsToStr($totalTimes) : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card gradient-bottom">
                <div class="card-header">
                    <h4>@lang('module_lead.interestedNotInterested')</h4>
                </div>
                <div class="card-body" id="top-5-scroll">
                    <ul class="list-unstyled list-unstyled-border">
                        @foreach($userActiveCampaignsDataLists as $activeCampaignsDataList)
                            <li class="media">
                                <img class="mr-3 rounded" width="55" src="{{ asset('assets/img/products/product-3-50.png') }}" alt="product">
                                <div class="media-body">
                                    <div class="float-right"><div class="font-weight-600 text-muted text-small">{{ $activeCampaignsDataList['totalLeads'] }} @lang('app.calls')</div></div>
                                    <div class="media-title"><a href="{{ route('admin.campaigns.show', md5($activeCampaignsDataList['id'])) }}">{{ $activeCampaignsDataList['name'] }}</a></div>
                                    <div class="mt-1">
                                        <div class="budget-price">
                                            <div class="budget-price-square bg-primary" data-width="64%"></div>
                                            <div class="budget-price-label">{{ $activeCampaignsDataList['interested'] }}</div>
                                        </div>
                                        <div class="budget-price">
                                            <div class="budget-price-square bg-danger" data-width="43%"></div>
                                            <div class="budget-price-label">{{ ($activeCampaignsDataList['totalLeads'] - $activeCampaignsDataList['interested']) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer pt-3 d-flex justify-content-center">
                    <div class="budget-price justify-content-center">
                        <div class="budget-price-square bg-primary" data-width="20"></div>
                        <div class="budget-price-label">@lang('module_lead.interested')</div>
                    </div>
                    <div class="budget-price justify-content-center">
                        <div class="budget-price-square bg-danger" data-width="20"></div>
                        <div class="budget-price-label">@lang('module_lead.notInterested')</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4>@lang('app.last7Days')</h4>
                </div>
                <div class="card-body">
                    <canvas id="myChart" height="158"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>@lang('module_campaign.bookedApointment')</h4>
                    <div class="card-header-action">
                        <a href="{{ route('admin.appointments.index') }}" class="btn btn-danger">@lang('app.viewAll') <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive table-invoice">
                        <table class="table table-striped">
                            <tr>
                                <th>@lang('app.campaign')</th>
                                <th>@lang('module_campaign.salesMember')</th>
                                <th>@lang('module_campaign.appointmentTime')</th>
                                <th>@lang('app.action')</th>
                            </tr>
                            @foreach($bookedAppointments as $bookedAppointment)
                                <tr>
                                    <td class="font-weight-600"><a href="{{ route('admin.campaigns.show', md5($bookedAppointment->campaign_id)) }}">{{ $bookedAppointment->campaign_name }}</a></td>
                                    <td>{{ trim($bookedAppointment->first_name .' ' . $bookedAppointment->last_name) }}</td>
                                    <td>{{ $bookedAppointment->appointment_time->timezone($user->timezone)->format($user->date_format .' ' . $user->time_format) }}</td>
                                    <td>
                                        <a href="{{ route('admin.callmanager.lead', [md5($bookedAppointment->lead_id)]) }}" class="btn btn-icon btn-success"
                                           data-toggle="tooltip" data-original-title="@lang('module_call_enquiry.goAndResumeCall')"><i class="fas fa-play" aria-hidden="true"></i></a>

                                        <a href="javascript:void(0);" onclick="viewLead('{{ md5($bookedAppointment->lead_id) }}')" class="btn btn-icon btn-info"
                                           data-toggle="tooltip" data-original-title="@lang('module_call_enquiry.viewLead')"><i class="fas fa-eye" aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-hero">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-phone-volume"></i>
                    </div>
                    <h4>{{ $pendingCallbacks->count() }}</h4>
                    <div class="card-description">@lang('menu.pendingCallbacks')</div>
                </div>
                <div class="card-body p-0">
                    <div class="tickets-list">
                        <ul class="list-unstyled list-unstyled-border ticket-item">
                            @foreach($pendingCallbacks as $pendingCallback)
                                <li class="media">
                                    <div class="media-body">
                                        <div class="mb-1 float-right">
                                            <div class="buttons">
                                                <a href="{{ route('admin.callmanager.lead', [md5($pendingCallback->lead_id)]) }}" class="btn btn-icon btn-sm btn-success"
                                                   data-toggle="tooltip" data-original-title="@lang('module_call_enquiry.goAndResumeCall')"><i class="fas fa-play" aria-hidden="true"></i></a>

                                                <a href="javascript:void(0);" onclick="viewLead('{{ md5($pendingCallback->lead_id) }}')" class="btn btn-icon btn-sm btn-info"
                                                   data-toggle="tooltip" data-original-title="@lang('module_call_enquiry.viewLead')"><i class="fas fa-eye" aria-hidden="true"></i></a>
                                            </div>
                                        </div>
                                        <h6 class="media-title"><a href="{{ route('admin.campaigns.show', md5($pendingCallback->campaign_id)) }}">{{ $pendingCallback->campaign_name }}</a></h6>
                                        <div class="text-small text-muted">
                                            @php($newName = \App\Classes\Common::getLeadDataByColumn($pendingCallback->lead_id, $nameArray))
                                            @if($newName !== '')
                                                {{ $newName }}
                                            @else
                                                {{ \App\Classes\Common::getLeadDataByColumn($pendingCallback->lead_id, $firstNameArray) }} {{ \App\Classes\Common::getLeadDataByColumn($pendingCallback->lead_id, $lastNameArray) }}
                                            @endif
                                            <div class="bullet"></div> <span class="text-primary">{{ $pendingCallback->callback_time->timezone($user->timezone)->format($user->date_format .' ' . $user->time_format) }}</span></div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('admin.pending-callback.index') }}" class="ticket-item ticket-more">
                            @lang('app.viewAll') <i class="fas fa-chevron-right"></i>
                        </a>
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
    <script src="{{ asset('assets/modules/chart.min.js') }}"></script>

    <script>

        function viewLead (id) {
            var url = '{{ route('admin.callmanager.view-lead', ':id') }}';
            url      = url.replace(':id',id);
            $.ajaxModal('#addEditModal', url)
        }

        var ctx = document.getElementById("myChart").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dateArray) !!},
                datasets: [{
                    label: "{{ trans('app.calls') }}",
                    data: {!! json_encode($leadCountArray) !!},
                    borderWidth: 2,
                    backgroundColor: 'rgba(63,82,227,.8)',
                    borderColor: 'transparent',
                    pointBorderWidth: 0,
                    pointRadius: 3.5,
                    pointBackgroundColor: 'transparent',
                    pointHoverBackgroundColor: 'rgba(63,82,227,.8)',
                }]
            },
            options: {
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            // display: false,
                            drawBorder: false,
                            color: '#f2f2f2',
                        },
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1,
                            callback: function(value, index, values) {
                                return value;
                            }
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false,
                            tickMarkLength: 15,
                        }
                    }]
                },
            }
        });
    </script>
@endsection