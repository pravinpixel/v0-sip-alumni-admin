<?php

namespace App\Console\Commands;

use App\Mail\IAlertURBillingAccountInvoice;
use App\Models\Employee;
use App\Models\Iallert;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class IalertBACReminder extends Command
{
    protected $signature = 'app:ialert-bac-remider';
    protected $description = 'Send Ialert BAC  Reminder';

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        try {
            echo "Command started...";
            $ialertInvoice =  Iallert::with('documents')
                ->where('wcr_status', '1')
                ->where('bill_ac_status', '0')
                ->whereDate('ba_customer_commitment_date', '<=', now()->format('Y-m-d'))
                ->get();
            if ($ialertInvoice->isNotEmpty()) {
                foreach ($ialertInvoice as $invoice) {
                    $invoice['auth_user'] = Employee::where('id', $invoice->updated_by)->first();
                    $setting = Setting::where('name', 'signature')->first();
                    $invoice['signature_logo'] = $setting ? $setting->value : null;
                    $customer_emails = is_array($invoice->customer_email) ? $invoice->customer_email : explode(',', $invoice->customer_email);
                    $customer_emails = array_filter(array_map('trim', $customer_emails));
                    $customer_emails = array_unique($customer_emails);
                    if ($invoice->os_value != 0) {
                        try {
                            Mail::mailer('ialert_smtp')->to($customer_emails)->send(new IAlertURBillingAccountInvoice($invoice));
                        } catch (\Exception $e) {
                            // Log the exception but do not break the flow
                            Log::channel('cron')->info('BAC Reminder failed notification failed: ' . $e->getMessage());
                        }
                    }
                }
                echo "Command ended...";
            } else {
                Log::channel('cron')->info('No Invoices Found');
            }
        } catch (\Exception $e) {
            // Log the exception but do not break the flow
            Log::channel('cron')->info('BAC Reminder failed: ' . $e->getMessage());
        }
        $this->info('BAC Reminder have been sent successfully.');
    }
}
