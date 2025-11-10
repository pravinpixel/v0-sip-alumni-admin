<?php

namespace App\Console\Commands;

use App\Mail\IAlertCustomerFollowup;
use App\Models\Employee;
use App\Models\Iallert;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class IalertCustomerFolloupReminder extends Command
{
    protected $signature = 'app:ialert-customer-followup-reminder';
    protected $description = 'Send Ialert Customer Followup Reminder';

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
                ->where('bill_ac_status', '1')
                ->whereDate('customer_follow_up_date', '<=', now()->format('Y-m-d'))
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
                            Mail::mailer('ialert_smtp')->to($customer_emails)->send(new IAlertCustomerFollowup($invoice));
                        } catch (\Exception $e) {
                            // Log the exception but do not break the flow
                            Log::channel('cron')->info('Customer Followup Reminder failed notification failed: ' . $e->getMessage());
                        }
                     }
                }
                echo "Command ended...";
            } else {
                Log::channel('cron')->info('No Invoices Found');
            }
        } catch (\Exception $e) {
            // Log the exception but do not break the flow
            Log::channel('cron')->info('Customer Followup Reminder failed: ' . $e->getMessage());
        }
        $this->info('Customer Followup Reminder have been sent successfully.');
    }

}
