<?php

namespace App\Mail;

use App\Helpers\UtilsHelper;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IAlertWCRStatusInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
        $setting = Setting::where('name', 'signature')->first();
        $this->data['signature_logo'] = $setting ? $setting->value : null;
    }

    public function build()
    {
        $subject = '🔴Invoice Notification / '.$this->data['customer_name'].' /'.$this->data['invoice_number'].' - USHA FIRE 🔴';
        $data = [
            'invoice' => $this->data
        ];

        $mailMessage = $this
            ->subject($subject)
            ->from(
                env('IALERT_MAIL_FROM_ADDRESS'),
                env('IALERT_MAIL_FROM_NAME', 'USHA FIRE')
            )
            ->view('emails.ialert-wcr-status-invoice', $data);

        $mailMessage->withSymfonyMessage(function ($message) {
            $customMessageId = $this->data['invoice_number'].'@taskmaster.designonline.in';
            $message->getHeaders()->addIdHeader('Message-ID', $customMessageId);
            $message->getHeaders()->addTextHeader('In-Reply-To', $customMessageId);
            $message->getHeaders()->addTextHeader('References', $customMessageId);
        });

        $cc = UtilsHelper::addIalertCC($this->data);
        if ($cc) {
            $mailMessage->cc($cc);
        }

        return $mailMessage;

    }
}

