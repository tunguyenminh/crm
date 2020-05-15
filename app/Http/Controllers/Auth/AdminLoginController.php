<?php

namespace App\Http\Controllers\Auth;

use App\Classes\Reply;
use App\Http\Controllers\MainBaseController;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Http\Requests\Auth\FrontLoginRequest;
use App\Models\User;

class AdminLoginController extends MainBaseController
{

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $this->pageTitle = trans('app.login');

        if(!$this->isLegal()){
            return redirect('verify-purchase');
        }

        // If a user is already logged in, redirect to dashboard Page
        if(auth()->guard('admin')->check()) {
            return \Redirect::route('admin.dashboard.index');
        }

        return view('admin.login', $this->data);
    }

    /**
     * @param FrontLoginRequest $request
     * @return array
     */

    public function ajaxLogin(AdminLoginRequest $request)
    {
        $email      = $request->get('email');
        $password   = $request->get('password');

        $user = User::where('email', $email)->first();

        if($user)
        {
            if ($user->status == 'waiting')
            {
                return Reply::error('messages.verificationPending');
            } else if ($user->status == 'disabled')
            {
                return Reply::error('messages.accountDisabled');
            }

            // Credentials to check user login
            $credentials = ['email' => $email, 'password' => $password, 'status' => 'enabled'];
            $remember    = $request->remember ? true : false;

            if (auth()->guard('admin')->attempt($credentials, $remember)) {
                $url = route('admin.dashboard.index');

                // User login success
                return Reply::redirect($url, 'messages.loginSuccess');

            }
        }

        // Login Failed
        return Reply::error('messages.loginFail');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        auth()->guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
