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

    @if($user->ability('admin', 'staff_create'))
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <a href="javascript:void(0);" onclick="addModal()" class="btn btn-icon icon-left btn-primary"><i class="fa fa-plus"></i> @lang('module_user.addNewUser') </a>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="users-table" width="100%">
                            <thead>
                            <tr>
                                <th>@lang('app.id')</th>
                                <th>@lang('module_user.name')</th>
                                <th>@lang('module_user.email')</th>
                                <th>@lang('app.createdAt')</th>
                                <th>@lang('app.status')</th>
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
    @if($user->ability('admin', 'staff_create,staff_edit'))
        @include('admin.includes.add-edit-modal')
    @endif
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
            ajax: '{!! route('admin.get-users') !!}',
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
                { data: 'id', name: 'id'},
                { data: 'first_name', name: 'first_name'},
                { data: 'email', name: 'email'},
                { data: 'created_at', name: 'created_at'},
                { data: 'status', name: 'status'},
                { data: 'action', name: 'action'}
            ]
        });

    });

    @if($user->ability('admin', 'staff_delete'))
        function deleteModal(id) {
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

                var url = "{{ route('admin.users.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'DELETE',
                    url: url,
                    data: {'_token': token},
                    success: function (response) {
                        if (response.status == "success") {
                            swal("@lang('app.deleted')!", response.message, "success");
                            table._fnDraw();
                        }
                    }
                });
            }
        });
    };
    @endif

    @if($user->ability('admin', 'staff_create,staff_edit'))

        @if($user->ability('admin', 'staff_create'))
            function addModal () {
            $.ajaxModal('#addEditModal','{{ route('admin.users.create') }}');
        }
        @endif

        @if($user->ability('admin', 'staff_edit'))
            function editModal (id) {
            var url = '{{ route('admin.users.edit', ':id') }}';
            url      = url.replace(':id',id);
            $.ajaxModal('#addEditModal', url)
        }
        @endif

        function addOrEditUser(id) {

        @if($user->ability('admin', 'staff_edit'))
        if(typeof id != 'undefined'){
            var url  ="{{route('admin.users.update',':id')}}";
            url      = url.replace(':id',id);
        }
        @endif

        @if($user->ability('admin', 'staff_create'))
        if (typeof id == 'undefined'){
            url = "{{ route('admin.users.store') }}";
        }
        @endif

        $.easyAjax({
            type: 'POST',
            url: url,
            file: true,
            container: "#user-add-edit-form",
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