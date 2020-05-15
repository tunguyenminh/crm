@foreach($formFields as $formField)
    <li class="media sortable_{{  \Illuminate\Support\Str::snake(strtolower($formField->field_name)) }}">
        <div class="sort-handler mr-3">
            <i class="fas fa-th"></i>
        </div>
        <div class="media-body" data-value="{{ $formField->field_name }}">
            <div class="media-right">
                <a href="javascript:void(0);" onclick="removeThisField(this)" class="btn btn-icon btn-sm btn-danger"><i class="fas fa-trash"></i></a>
            </div>
            <div class="media-title mb-1">{{ $formField->field_name }}</div>
        </div>
    </li>
@endforeach