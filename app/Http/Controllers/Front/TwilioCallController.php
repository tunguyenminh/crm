<?php

namespace App\Http\Controllers\Front;

use App\Models\Setting;
use App\Models\TwilioNumber;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Twilio\Jwt\ClientToken;
use Illuminate\Routing\Controller;
use Twilio\TwiML\VoiceResponse;

class TwilioCallController extends Controller
{
    /**
     * Create a new capability token
     *
     * @return \Illuminate\Http\Response
     */
    public function newToken(Request $request)
    {
        $setting = Setting::first();

        $clientToken = new ClientToken($setting->twilio_account_sid, $setting->twilio_auth_token);

        $forPage = $request->input('forPage');
        $applicationSid = $setting->twilio_application_sid;
        $clientToken->allowClientOutgoing($applicationSid);


        $supportAgent = 'support_agent';

        if(auth()->guard('admin')->check())
        {
            $user = auth()->guard('admin')->user();

            $supportAgent = $user->twilio_client_name;
        }

//        if ($forPage === route('dashboard', [], false)) {
            $clientToken->allowClientIncoming($supportAgent);
//        } else {
//            $clientToken->allowClientIncoming('customer');
//        }

        $token = $clientToken->generateToken();
        return response()->json(['token' => $token]);
    }

    /**
     * Process a new call
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function newCall(Request $request)
    {
        $twiloNumber = TwilioNumber::first();
//        $response = new Twiml();
        $callerIdNumber = $twiloNumber->number;
//
//        $dial = $response->dial(['callerId' => $callerIdNumber]);
//
        $phoneNumberToDial = $request->input('phoneNumber');
//
//        if (isset($phoneNumberToDial)) {
//            $dial->number($phoneNumberToDial);
//        } else {
//            $dial->client('support_agent');
//        }
//
//        //$response->record(['timeout' => 0]);
//
//        return $response;

//        $response = new VoiceResponse();
//        $dial = $response->dial($callerIdNumber);
//        $dial->number($phoneNumberToDial);
//
//        return  $response;

        $response = new VoiceResponse();
        $dial = $response->dial('', ['callerId' => $callerIdNumber, 'record' => 'record-from-answer-dual']);
        $dial->number($phoneNumberToDial);

        return $response;
    }

    public function inboundWebhookHandler($numberMd5)
    {
        $twiloNumber = TwilioNumber::whereRaw('md5(id) = ?', $numberMd5)->first();

        $callerOption = [
            'callerId' => $twiloNumber->number,
        ];

        if($twiloNumber->inbound_recording != 'do-not-record')
        {
            $callerOption['record'] = $twiloNumber->inbound_recording;
        }

        $response = new VoiceResponse();
//        $dial = $response->dial('');
        $dial = $response->dial('', $callerOption);

        // Take last 10 logged in users
        $users = User::orderBy('updated_at', 'desc')->take(10)->get();
        foreach ($users as $user)
        {
            $dial->client($user->twilio_client_name);
        }

        return $response;
    }

}
