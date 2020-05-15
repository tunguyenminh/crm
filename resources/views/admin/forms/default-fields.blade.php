@foreach($defaultFormFields as $defaultFormField)
    <div class="col-md-6">
        <div class="custom-control custom-checkbox mb-2">
            <input type="checkbox" class="custom-control-input selectField" onclick="formFieldSelected(this)" data-field-value="{{ \Illuminate\Support\Str::snake(strtolower($defaultFormField)) }}" id="form_field_{{ \Illuminate\Support\Str::snake(strtolower($defaultFormField)) }}" name="form_field_{{ $defaultFormField }}" value="{{ $defaultFormField }}" @if(in_array($defaultFormField, $formFieldSelected)) checked @endif>
            <label class="custom-control-label" for="form_field_{{ \Illuminate\Support\Str::snake(strtolower($defaultFormField)) }}">{{ $defaultFormField }}</label>
        </div>
    </div>
@endforeach