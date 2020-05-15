<?php

namespace App\Http\Controllers\Admin;


use App\Classes\Common;

use App\Http\Requests\Admin\Forms\DeleteRequest;
use App\Http\Requests\Admin\Forms\IndexRequest;
use App\Http\Requests\Admin\Forms\StoreRequest;
use App\Http\Requests\Admin\Forms\UpdateRequest;

use App\Classes\Reply;
use App\Models\EmailTemplate;
use App\Models\Form;
use App\Models\FormField;
use Illuminate\Http\Request;

class FormController extends AdminBaseController
{
     /**
	 * UserController constructor.
	 */

    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = trans('menu.formBuilder');
        $this->pageIcon = 'fa fa-address-card';
        $this->settingsMenuActive = 'active';
        $this->formActive = 'active';
        $this->bootstrapModalRight = false;
        $this->defaultFormFields = ['First Name', 'Last Name', 'Company Name', 'Website', 'Notes', 'Email', 'Phone No', 'Mobile No', 'Telephone No'];
    }

    public function index(IndexRequest $request)
    {
        return view('admin.forms.index', $this->data);
    }

    public function getLists()
    {

        $results = Form::select('forms.id', 'forms.form_name', 'forms.created_by', 'forms.created_at')
                     ->with('creator', 'fields');

        // Fetch data according to permission assigned
        if(!$this->user->ability('admin', 'form_view_all')) {
            $results = $results->where('forms.created_by', $this->user->id);
        }

        return datatables()->eloquent($results)
            ->addColumn('creator', function ($row) {
                return $row->creator ? Common::getUserWidget($row->creator) : '-';
            })
            ->addColumn('fields', function ($row) {
                $string = '<ul>';

                foreach ($row->fields as $field)
                {
                    $string .= '<li>'.$field->field_name.'</li>';
                }
                return $string;
            })
            ->editColumn(
                'created_at',
                function ($row) {
                    return $row->created_at->format('d F, Y');
                }
            )
            ->addColumn('action', function ($row) {
                $text = '<div class="buttons">';

                if($this->user->ability('admin', 'form_edit')) {
                    $text .= '<a href="' . route('admin.forms.edit', $row->id) . '" class="btn btn-info btn-icon icon-left"
                      data-toggle="tooltip" data-original-title="' . trans('app.edit') . '"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                }

                if($this->user->ability('admin', 'form_delete')) {
                    $text .= '<button onclick="deleteModal(' . $row->id . ')" class="btn btn-danger btn-icon icon-left"
                      data-toggle="tooltip" data-original-title="' . trans('app.delete') . '"><i class="fa fa-trash" aria-hidden="true"></i></button>';
                }

                $text .= '</div>';

                return $text;
            })
            ->rawColumns(['creator', 'fields', 'action'])
            ->make(true);
    }

    public function create()
    {
        $this->pageTitle = trans('module_form.createForm');
        $this->icon = 'plus';

        $this->form = new Form();
        $this->allFormLists = Form::all();
        $this->formFields = [];
        $this->formFieldSelected = [];
        return view('admin.forms.add-edit', $this->data);
    }

    public function store(StoreRequest $request)
    {

        \DB::beginTransaction();

        $form = new Form();
        $form->created_by = $this->user->id;
        $this->storeAndUpdate($form, $request);

        \DB::commit();

        return Reply::redirect(route('admin.forms.index'), 'messages.createSuccess');

    }

    public function edit($id)
    {
        $this->pageTitle = trans('module_form.editForm');
        $this->icon = 'edit';

        $form = Form::findOrFail($id);

        // Check if current email template by logged in user
        // and not have permission to see all email templates
        if(!$this->user->ability('admin', 'form_view_all') && $form->created_by != $this->user->id) {
            return response()->view($this->forbiddenErrorView);
        }

        $this->form = $form;
        $this->allFormLists = Form::all();
        $this->formFields = FormField::select('form_fields.*')
                                    ->join('forms', 'forms.id', '=', 'form_fields.form_id')
                                    ->where('forms.id', '=', $id)
                                    ->orderBy('form_fields.order')
                                    ->get();

        $this->formFieldSelected = FormField::select('form_fields.*')
                                            ->join('forms', 'forms.id', '=', 'form_fields.form_id')
                                            ->where('forms.id', '=', $id)
                                            ->orderBy('form_fields.order')
                                            ->pluck('field_name')
                                            ->toArray();

        // Call the same create view for edit
        return view('admin.forms.add-edit', $this->data);
    }

    public function update(UpdateRequest $request,$id)
    {
        \DB::beginTransaction();

        $form         = Form::findOrFail($id);

        // Check if current email template by logged in user
        // and not have permission to see all email templates
        if(!$this->user->ability('admin', 'form_view_all') && $form->created_by != $this->user->id) {
            return Reply::error('messages.notAllowed');
        }

        $this->storeAndUpdate($form, $request);

        \DB::commit();

        return Reply::redirect(route('admin.forms.index'), 'messages.updateSuccess');

    }

    public function destroy(DeleteRequest $request, $id)
    {
        $form = Form::findOrFail($id);

        // Check if current email template by logged in user
        // and not have permission to see all email templates
        if(!$this->user->ability('admin', 'form_view_all') && $form->created_by != $this->user->id) {
            return Reply::error('messages.notAllowed');
        }

        $form->delete();
        return Reply::success('messages.deleteSuccess');
    }

    private function  storeAndUpdate($form, $request)
    {
        $form->form_name   = $request->form_name;
        $form->save();

        $formFields = $request->fields;
        $order = 1;

        foreach ($formFields as $formField)
        {
            $newFormField = FormField::where('field_name', trim($formField))
                                     ->where('form_id', '=', $form->id)
                                     ->first();

            if(!$newFormField)
            {
                $newFormField = new FormField();
                $newFormField->form_id = $form->id;
            }

            $newFormField->field_name = $formField;
            $newFormField->order = $order;
            $newFormField->save();

            $order++;
        }
    }

    public function selectFormData(Request $request)
    {
        $form = Form::find($request->selected_form_id);

        $this->formFields = FormField::where('form_id', $request->selected_form_id)
                                ->orderBy('order', 'asc')
                                ->get();
        $this->formFieldSelected = FormField::where('form_id', $request->selected_form_id)
                                            ->orderBy('order', 'asc')
                                            ->pluck('field_name')
                                            ->toArray();

        $output = view('admin.forms.form-fields', $this->data)->render();
        $outputDefaultFields = view('admin.forms.default-fields', $this->data)->render();

        return Reply::success('messages.dataFetchedSuccessfully', ['html' => $output, 'html1' => $outputDefaultFields,'form' => $form, 'formFields' => $this->formFields]);
    }

    public function uploadFieldsFromCSV(Request $request)
    {
        $file = fopen($request->import_from_csv, 'r');
        $row = 0;
        $csvFormFields = [];

        while(($lineArrayResults = fgetcsv($file)) !== FALSE)
        {

            if($row == 0)
            {
                foreach ($lineArrayResults as $lineArrayResultKey => $lineArrayResult)
                {
                    $csvFormFields[$lineArrayResultKey] = $lineArrayResult;
                }

                break;
            }

            $row++;
        }

        fclose($file);

        $this->csvFormFields = $csvFormFields;
        $this->formFieldSelected = $csvFormFields;

        $output = view('admin.forms.csv-form-fields', $this->data)->render();
        $outputDefaultFields = view('admin.forms.default-fields', $this->data)->render();

        return Reply::success('messages.csvFieldImportedSuccessfully', ['html' => $output, 'html1' => $outputDefaultFields]);
    }

    public function addNewField()
    {
        $this->icon = 'plus';

        return view('admin.forms.add-field', $this->data);
    }
}
