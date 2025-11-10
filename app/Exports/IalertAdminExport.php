<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\BranchLocation;
use App\Models\Employee;
use App\Models\Iallert;
use App\Models\PaymentCommitedOn;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;

class IalertAdminExport implements FromQuery, WithHeadings, WithMapping
{
    private $serialNumber = 0;

    public function query()
    {
        ini_set('max_execution_time', 600);
        $query = Iallert::where('os_value', '!=', 0)
            ->withCount(['tasks'])
            ->orderByDesc('id');

        return $query;
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Doc Entry',
            'Branch ID',
            'BDE Employee ID',
            'BDE Name',
            'Company Name',
            'Invoice Number',
            'Invoice Value',
            'OS Value',
            'Invoice Date',
            'Age',
            'Invoice pdf',
            'SAP Attachments',
            'Logistic WCR Status',
            'Portal Invoice',
            'Order Type',
            'PO Reference',
            'Payment Terms',
            'Balance Remarks',
            'Einvoice Number',
            'Contact Person',
            'Mobile',
            'Email ID',
            'CS Code',
            'Branch',
            'Default Customer Email',
            'Additional Emails',
            'WCR Status',
            'WC Commitment Date',
            'Bill Accounted Status',
            'BA Customer Commitment Date',
            'Invoice Status',
            'Customer Follow up Date',
            'Payment Committed On',
            'PC Revised Count',
            'Tough Nut Status',
            'RNR',
            'Internal Follow up Date',
            'Remarks For email',
            'No of Tasks',
            'invoice link',
            'BDE Email id',
            'Manager Email id',
            'ART Email id',
            'Logistic Email id',
            'ART Head Email id',
            'Updated by',
            'Created at',
            'Updated at',
        ];
    }

    public function map($ialert): array
    {
        $this->serialNumber++;
        $attach = '=HYPERLINK("' . $ialert->sap_attachments . '")';
        $pdf = '=HYPERLINK("' . $ialert->invoice_pdf . '")';
        $inv_link = '=HYPERLINK("' . config('app.task_url') . '/task?invoice_id=' . $ialert->id . '&tab=ialert")';
        $payment_revision_count = PaymentCommitedOn::where('ialert_id', $ialert->id)
            ->where(function ($query) {
                $query->whereNotNull('old_value')
                    ->whereNotNull('new_value');
            })
            ->count();
        return [
            $this->serialNumber,
            $ialert->doc_entry ?? '',
            $ialert->branch_id ?? '',
            $ialert->bde_id ?? '',
            $ialert->bde_name ?? '',
            $ialert->customer_name ?? '',
            $ialert->invoice_number ?? '',
            $ialert->invoice_value ?? '',
            $ialert->os_value ?? '',
            $ialert->invoice_date ? \Carbon\Carbon::parse($ialert->invoice_date)->format('d-m-Y') : '',
            $ialert->age ?? '',
            $pdf ?? '',
            $attach ?? '',
            $ialert->logistic_wcr_status ?? '',
            $ialert->portal_invoice ?? '',
            $ialert->order_type ?? '',
            $ialert->po_reference ?? '',
            $ialert->payment_terms ?? '',
            $ialert->balance_remarks ?? '',
            $ialert->einvoice_number ?? '',
            $ialert->contact_person ?? '',
            $ialert->mobile ?? '',
            $ialert->email_id ?? '',
            $ialert->customer_code ?? '',
            $ialert->branch ?? '',
            $ialert->customer_email ?? '',
            $ialert->additional_emails ?? '',
            $ialert->wcr_status == '1' ? 'Yes' : ($ialert->wcr_status == '0' ? 'No' : ''),
            $ialert->wc_date ? \Carbon\Carbon::parse($ialert->wc_date)->format('d-m-Y') : '',
            $ialert->bill_ac_status == '1' ? 'Yes' : ($ialert->bill_ac_status == '0' ? 'No' : ''),
            $ialert->ba_customer_commitment_date ? \Carbon\Carbon::parse($ialert->ba_customer_commitment_date)->format('d-m-Y') : '',
            $ialert->invoice_status ?? '',
            $ialert->customer_follow_up_date ? \Carbon\Carbon::parse($ialert->customer_follow_up_date)->format('d-m-Y') : '',
            $ialert->payment_commited_on ? \Carbon\Carbon::parse($ialert->payment_commited_on)->format('d-m-Y') : '',
            $payment_revision_count > 0 ? $payment_revision_count : '',
            $ialert->tough_nut_status == '1' ? 'Yes' : ($ialert->tough_nut_status == '0' ? 'No' : ''),
            $ialert->rnr ? \Carbon\Carbon::parse($ialert->rnr)->format('d-m-Y') : '',
            $ialert->internal_follow_up_date ? \Carbon\Carbon::parse($ialert->internal_follow_up_date)->format('d-m-Y') : '',
            $ialert->remark_for_email ?? '',
            $ialert->tasks_count ?? 0,
            $inv_link ?? '',
            $ialert->bde_email_id ?? '',
            $ialert->manager_email_id ?? '',
            $ialert->art_email_id ?? '',
            $ialert->logistics_email_id ?? '',
            $ialert->art_head_email_id ?? '',
            $ialert->updated_by ??  '',
            $ialert->created_at ? \Carbon\Carbon::parse($ialert->created_at)->format('d-m-Y') : '',
            $ialert->updated_at ? \Carbon\Carbon::parse($ialert->updated_at)->format('d-m-Y') : '',


            //            $ialert->customer_code ?? '-',
            // $ialert->customer_name ?? '-',
            // $ialert->po_reference ?? '-',
            // $ialert->payment_terms ?? '-',
            // $ialert->balance_remarks ?? '-',
            // $ialert->contact_person ?? '-',
            // $ialert->mobile ?? '-',
            // $ialert->email_id ?? '-',
            // $ialert->logistic_wcr_status ?? '-',
            //            $ialert->portal_invoice ?? '-',
            // $attach ?? '-',
            // $pdf ?? '-',
            // $ialert->invoice_value ?? '-',
            // $ialert->os_value ?? '-',
            // $ialert->age ?? '-',
            //            $ialert->bde_email_id ?? '-',
            //            $ialert->manager_email_id ?? '-',
            //            $ialert->art_email_id ?? '-',
            //            $ialert->logistics_email_id ?? '-',
            //            $ialert->art_head_email_id ?? '-',


            // $ialert->customer_email,
            // $ialert->additional_emails,
            // $ialert->wcr_status == '1' ? 'Yes' : ($ialert->wcr_status == '0' ? 'No' : '-'),
            // $ialert->wc_date ? \Carbon\Carbon::parse($ialert->wc_date)->format('d-m-Y') : '-',
            // $ialert->bill_ac_status == '1' ? 'Yes' : ($ialert->bill_ac_status == '0' ? 'No' : '-'),
            // $ialert->ba_customer_commitment_date ? \Carbon\Carbon::parse($ialert->ba_customer_commitment_date)->format('d-m-Y') : '-',
            // $ialert->invoice_status,
            // $ialert->customer_follow_up_date ? \Carbon\Carbon::parse($ialert->customer_follow_up_date)->format('d-m-Y') : '-',
            // $ialert->payment_commited_on ? \Carbon\Carbon::parse($ialert->payment_commited_on)->format('d-m-Y') : '-',
            // $payment_revision_count > 0 ? $payment_revision_count : '-',
            // $ialert->tough_nut_status == '1' ? 'Yes' : ($ialert->tough_nut_status == '0' ? 'No' : '-'),
            // $ialert->rnr ? \Carbon\Carbon::parse($ialert->rnr)->format('d-m-Y') : '-',
            // $ialert->internal_follow_up_date ? \Carbon\Carbon::parse($ialert->internal_follow_up_date)->format('d-m-Y') : '-',
            // $ialert->remark_for_email,
            // $ialert->tasks_count ?? 0,
            // $inv_link ?? '-',
        ];
    }
}
