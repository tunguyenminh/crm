<?php

namespace App\Http\Controllers\Admin;


use App\Classes\Common;

use App\Http\Requests\Admin\SalesMember\DeleteRequest;
use App\Http\Requests\Admin\SalesMember\IndexRequest;
use App\Http\Requests\Admin\SalesMember\StoreRequest;
use App\Http\Requests\Admin\SalesMember\UpdateRequest;

use App\Classes\Reply;
use App\Models\SalesMember;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class SalesMemberController extends AdminBaseController
{
     /**
	 * UserController constructor.
	 */

    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = trans('menu.salesMembers');
        $this->pageIcon = 'fa fa-user-tie';
        $this->userManagementMenuActive = 'active';
        $this->salesMemberActive = 'active';
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(IndexRequest $request)
    {

        return view('admin.users.sales.index', $this->data);
    }

     /**
	 * @return mixed
	 */
    public function getLists()
    {

        $users = SalesMember::select('id', 'image', 'first_name', 'last_name', 'email', 'created_at');

        return datatables()->eloquent($users)
            ->editColumn('first_name', function ($row) {
                return Common::getUserWidget($row);
            })
            ->editColumn(
                'email',
                function ($row) {
                    $data = $row->email. ' ';

                    if($row->email_verified == 'yes') {
                        $data .= '<i class="fa fa-check-circle" style="color: green;"></i>';
                    }

                    return $data;
                }
            )
            ->editColumn(
                'created_at',
                function ($row) {
                    return $row->created_at->format('d F, Y');
                }
            )
            ->addColumn('action', function ($row) {
                $text = '<div class="buttons">';

                    if($this->user->ability('admin', 'sales_member_edit')) {
                        $text .= '<a href="javascript:void(0);" onclick="editModal(' . $row->id . ')" class="btn btn-info btn-icon icon-left"
                          data-toggle="tooltip" data-original-title="' . trans('app.edit') . '"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    }

                    if($this->user->ability('admin', 'sales_member_delete')) {
                        $text .= '<button onclick="deleteModal(' . $row->id . ')" class="btn btn-danger btn-icon icon-left"
                          data-toggle="tooltip" data-original-title="' . trans('app.delete') . '"><i class="fa fa-trash" aria-hidden="true"></i></button>';
                    }

                $text .= '</div>';

                return $text;
            })
            ->rawColumns(['first_name', 'action', 'email'])
            ->make(true);
    }

    public function create()
    {

        $this->icon = 'plus';

        $this->userDetails = new SalesMember();
        return view('admin.users.sales.add-edit', $this->data);
    }

    public function store(StoreRequest $request)
    {

        \DB::beginTransaction();

        $user = new SalesMember();
        $this->storeAndUpdate($user, $request);

        \DB::commit();
        return Reply::success('messages.createSuccess');

    }

    public function edit($id)
    {

        $this->icon = 'edit';
        $this->userDetails = SalesMember::find($id);

        // Call the same create view for edit
        return view('admin.users.sales.add-edit', $this->data);
    }

    public function update(UpdateRequest $request,$id)
    {

        \DB::beginTransaction();

        $user         = SalesMember::find($id);
        $this->storeAndUpdate($user, $request);

        \DB::commit();
        return Reply::success('messages.updateSuccess');

    }

    public function destroy(DeleteRequest $request, $id)
    {
        $user  = SalesMember::find($id);

        //Deleting image
        $this->deleteUserImage($user->image);

        $user->delete();
        return Reply::success('messages.deleteSuccess');
    }

    private function  storeAndUpdate($user, $request)
    {
        // If User Image uploaded
        if($request->hasFile('image'))
        {
            $largeLogo  = $request->file('image');

            $fileName   = 'sales_user_'.strtolower(str_random(20)).'.'.$largeLogo->getClientOriginalExtension();
            $largeLogo->move($this->userImagePath, $fileName);

            //Deleting previous image
            $this->deleteUserImage($user->image);

            $user->image        = $fileName;
        }

        if($request->password != '')
        {
            $user->password = Hash::make($request->password);
        }

        $user->first_name   = $request->first_name;
        $user->last_name   = $request->last_name;
        $user->email = $request->email;
        $user->contact_number = $request->contact_number;
        $user->skype_id = $request->skype_id;
        $user->save();
    }

    protected function deleteUserImage($imagePath)
    {
        if($imagePath != null) {
            if (File::exists($this->userImagePath . '/' . $imagePath))
            {
                Common::deleteCommonFiles($this->userImagePath . '/' . $imagePath);
            }
        }
    }

}
