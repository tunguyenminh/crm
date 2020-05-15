@foreach (\Illuminate\Support\Facades\File::files($updateFilePath) as $key=>$filename)
    @if (\Illuminate\Support\Facades\File::basename($filename) != "modules_statuses.json")
        <li class="list-group-item" id="file-{{ $key+1 }}">
            <div class="row">
                <div class="col-md-9">
                    {{ \Illuminate\Support\Facades\File::basename($filename) }}
                </div>
                <div class="col-md-3 text-right">
                    <button type="button" class="btn btn-success btn-sm btn-outline install-files" onclick="installUploadedFile('{{ $key+1 }}', '{{ $filename }}')">@lang('app.install') <i class="fa fa-upload"></i></button>

                    <button type="button" class="btn btn-danger btn-sm btn-outline delete-files" onclick="deleteUploadedFile('{{ $key+1 }}', '{{ $filename }}')">@lang('app.delete') <i class="fa fa-times"></i></button>
                </div>
            </div>
        </li>
    @endif
@endforeach