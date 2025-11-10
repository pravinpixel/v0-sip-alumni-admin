<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Faker\Factory as Faker;

class GenerateInvoiceJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:invoice-json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a JSON file with 4000 fake invoice records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('max_execution_time', 300);
        $faker = Faker::create();

        $records = [];
        for ($i = 0; $i < 4000; $i++) {
            $invoice_number = strval(20130611 + $i);
            $doc_entry = strval(617890 + $i);
            $branch_id = strval(rand(1, 20));
            $branch = chr(65 + ($i % 26)); // A-Z
            $sap_attachment_url = "http://115.160.248.84/Web_App_Invoive_Attachments/{$doc_entry}_20250527_060852";
            $invoice_pdf_url = $sap_attachment_url . "/";

            $records[] = [
                "invoice_number"      => $invoice_number,
                "doc_entry"           => $doc_entry,
                "branch_id"           => $branch_id,
                "branch"              => $branch,
                "bde_id"              => "NA",
                "bde_name"            => "{$branch_id}-{$branch}-OFFICE-{$faker->city()}",
                "order_type"          => "TRG",
                "invoice_date"        => $faker->date('Ymd'),
                "einvoice_number"     => strval(rand(1000000000, 9999999999)),
                "customer_code"       => strval(565656 + $i),
                "customer_name"       => $faker->company(),
                "po_reference"        => strval(rand(1000, 9999)),
                "payment_terms"       => $faker->randomElement(["30 Days", "45 Days", "60 Days"]),
                "balance_remarks"     => "null",
                "contact_person"      => $faker->name(),
                "mobile"              => substr($faker->numerify('##########'), 0, 10),
                "email_id"            => $faker->email(),
                "logistic_wcr_status" => "Yes",
                "portal_invoice"      => "",
                "sap_attachments"     => $sap_attachment_url,
                "invoice_pdf"         => $invoice_pdf_url,
                "invoice_value"       => rand(10000, 90000) . ".000000",
                "os_value"            => 0,
                "age"                 => "",
                "bde_email_id"        => $faker->email(),
                "manager_email_id"    => "NA",
                "art_email_id"        => implode(",", [$faker->email(), $faker->email(), $faker->email()]),
                "logistics_email_id"  => "NA",
                "art_head_email_id"   => "NA",
            ];
        }

        $data = ["iallerts" => $records];

        // Save file in storage/app/
        $filePath = storage_path('app/invoice_data_4000.json');
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

        $this->info("✅ JSON file generated successfully: {$filePath}");
    }
}
