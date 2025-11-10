<?php

namespace App\Console\Commands;

use App\Mail\IAlertWCRStatusInvoice;
use App\Models\Employee;
use App\Models\Iallert;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class IalertWCRReminder extends Command
{
    protected $signature = 'app:ialert-wcr-reminder';
    protected $description = 'Send Ialert WCR Reminder';

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        try {
            echo "Command started...";
            $ialertInvoice =  Iallert::with('documents')
                ->where('wcr_status', '0')
                ->whereDate('wc_date', '<=', now()->format('Y-m-d'))
                ->get();
            if ($ialertInvoice->isNotEmpty()) {
                foreach ($ialertInvoice as $invoice) {
                    $invoice['auth_user'] = Employee::where('id', $invoice->updated_by)->first();
                    $setting = Setting::where('name', 'signature')->first();
                    $invoice['signature_logo'] = $setting ? $setting->value : null;
                    
                    if ($invoice->os_value != 0) {
                        try {
                            Mail::mailer('ialert_smtp')->to($invoice->logistics_email_id)->send(new IAlertWCRStatusInvoice($invoice));
                        } catch (\Exception $e) {
                            // Log the exception but do not break the flow
                            Log::channel('cron')->info('WCR Reminder failed notification failed: ' . $e->getMessage());
                        }
                    }
                    
                }
                echo "Command ended...";
            } else {
                Log::channel('cron')->info('No Invoices Found');
            }
        } catch (\Exception $e) {
            // Log the exception but do not break the flow
            Log::channel('cron')->info('WCR Reminder failed: ' . $e->getMessage());
        }
        $this->info('WCR Reminder have been sent successfully.');
    }

}
