<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    protected $table = 'email_settings';

    public function verifySmtp()
    {
        if($this->mail_driver =='smtp'){
            try {
                $transport = new \Swift_SmtpTransport($this->mail_host, $this->mail_port, $this->mail_encryption);
                $transport->setUsername($this->mail_username);
                $transport->setPassword($this->mail_password);

                $mailer = new \Swift_Mailer($transport);
                $mailer->getTransport()->start();

                if($this->verified == 0){
                    $this->verified = 1;
                    $this->save();
                }

                return [
                    'success' => true,
                    'message' => __('messages.smtpSuccess')
                ];


            } catch (\Swift_TransportException $e) {
                $this->verified = 0;
                $this->save();
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];

            } catch (\Exception $e) {
                $this->verified = 0;
                $this->save();
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
    }
}
