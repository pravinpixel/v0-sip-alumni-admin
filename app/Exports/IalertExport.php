<?php

namespace App\Exports;

use App\Models\BranchLocation;
use App\Models\Employee;
use App\Models\Iallert;
use App\Models\PaymentCommitedOn;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;

class IalertExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;
    private $serialNumber = 0;

    public function __construct($filters = null)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $emp = Employee::find(Auth::id());
        if (!$emp) {
            return collect([]);
        }

        $branches = json_decode($emp->branch_id, true);
        $branches = is_array($branches) ? $branches : [$branches];
        $branch_codes = BranchLocation::whereIn('id', $branches)->pluck('branch_code')->toArray();

        $role_check = Role::where('id', $emp->role_id)->pluck('name')->toArray();

        $query = Iallert::whereIn('branch_id', $branch_codes)->where('os_value', '!=', 0)->withCount(['tasks']);

        if (in_array("Business Development Executive", $role_check)) {
            $query->where('bde_id', $emp->employee_id);
        }

        // Extract filters
        $filters = $this->filters;

        $search = $filters['search'];
        $bde_name = $filters['bde_name'] ?? [];
        $branch = $filters['branch'] ?? [];
        $invoice_number = $filters['invoice_number'] ?? [];
        $age = $filters['age'] ?? [];
        $value = $filters['value'] ?? [];
        $status = $filters['status'] ?? [];
        $followUp = $filters['follow_up'] ?? [];
        $customerName = $filters['customer_name'] ?? [];
        $sortColumn = $filters['sort_column'] ?? [];
        $sortOrder = $filters['sort_order'] ?? [];

        if (!empty($search)) {
            $query->where(function ($q) use ($filters) {
                $q->where('customer_name', 'like', "%" . $filters['search'] . "%")
                    ->orWhere('bde_name', 'like', "%" . $filters['search'] . "%")
                    ->orWhere('order_type', 'like', "%" . $filters['search'] . "%")
                    ->orWhere('invoice_number', 'like', "%" . $filters['search'] . "%")
                    ->orWhere('customer_code', 'like', "%" . $filters['search'] . "%")
                    ->orWhere('po_reference', 'like', "%" . $filters['search'] . "%")
                    ->orWhere('branch', 'like', "%" . $filters['search'] . "%");
            });
        }


        if (is_array($bde_name) && count($bde_name) > 0) {
            $query->whereIn('bde_name', $bde_name);
        }

        if (is_array($branch) && count($branch) > 0) {
            $query->whereIn('branch', $branch);
        }

        if (is_array($customerName) && count($customerName) > 0) {
            $query->whereIn('customer_name', $customerName);
        }

        if (is_array($invoice_number) && count($invoice_number) > 0) {
            $query->whereIn('invoice_number', $invoice_number);
        }

        if (is_array($age) && count($age) > 0) {
            $query->where(function ($q) use ($age) {
                foreach ($age as $data) {
                    switch ($data) {
                        case '0-7':
                            $q->orWhere('age', '<=', 7);
                            break;
                        case '30':
                            $q->orWhere('age', '<=', 30);
                            break;
                        case '30-60':
                            $q->orWhereBetween('age', [30, 60]);
                            break;
                        case '60-90':
                            $q->orWhereBetween('age', [60, 90]);
                            break;
                        case '90+':
                            $q->orWhere('age', '>=', 90);
                            break;
                        case '120+':
                            $q->orWhere('age', '>=', 120);
                            break;
                        case '150+':
                            $q->orWhere('age', '>=', 150);
                            break;
                        case '200+':
                            $q->orWhere('age', '>=', 200);
                            break;
                        case '365+':
                            $q->orWhere('age', '>=', 365);
                            break;
                    }
                }
            });
        }

        if ($value) {
            if (is_array($value) && count($value) > 0) {
                $query->where(function ($q) use ($value) {
                    foreach ($value as $data) {
                        switch ($data) {
                            case '1-1000':
                                $q->orWhere('invoice_value', '>=', 1)->where('invoice_value', '<=', 1000);
                                break;
                            case '1001-10000':
                                $q->orWhere('invoice_value', '>=', 1001)->where('invoice_value', '<=', 10000);
                                break;
                            case '10001-50000':
                                $q->orWhere('invoice_value', '>=', 10001)->where('invoice_value', '<=', 50000);
                                break;
                            case '50001-100000':
                                $q->orWhere('invoice_value', '>=', 50001)->where('invoice_value', '<=', 100000);
                                break;
                            case '100001-500000':
                                $q->orWhere('invoice_value', '>=', 100001)->where('invoice_value', '<=', 500000);
                                break;
                            case '500000+':
                                $q->orWhere('invoice_value', '>=', 500000);
                                break;
                        }
                    }
                });
            }
        }


        if (is_array($status) && count($status) > 0) {
            $query->where(function ($q) use ($status) {
                foreach ($status as $data) {
                    switch ($data) {
                        case 'wcr-blank':
                            $q->orWhereNull('wcr_status')->whereNotNull('customer_email');
                            break;
                        case 'wcr-no':
                            $q->orWhere('wcr_status', '0');
                            break;
                        case 'blank-org':
                            $q->where(function ($query) {
                                $query->whereHas('organization', function ($query) {
                                    $query->where(function ($query) {
                                        $query->where(function ($q) {
                                            $q->whereNull('primary_mail_id1')
                                                ->orWhere('primary_mail_id1', '');
                                        })->where(function ($q) {
                                            $q->whereNull('primary_mail_id2')
                                                ->orWhere('primary_mail_id2', '');
                                        });
                                    });
                                });
                            });
                            break;
                        case 'tds':
                            $q->orWhere('invoice_status', 'TDS');
                            break;
                        case 'tcs':
                            $q->orWhere('invoice_status', 'TCS');
                            break;
                        case 'ba-blank':
                            $q->orWhereNull('bill_ac_status')->where('wcr_status', '1');
                            break;
                        case 'ba-no':
                            $q->orWhere('bill_ac_status', '0');
                            break;
                        case 'tough-yes':
                            $q->orWhere('tough_nut_status', '1');
                            break;
                        case 'rnr':
                            $q->orWhereNotNull('rnr');
                            break;
                    }
                }
            });
        }

        $now = Carbon::now()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();

        if (is_array($followUp) && count($followUp) > 0) {
            $query->where(function ($q) use ($followUp, $now, $tomorrow, $endOfWeek) {
                foreach ($followUp as $followUpItem) {
                    switch ($followUpItem) {
                        case 'wc-overdue':
                            $q->orWhereDate('wc_date', '<=', $now)->where('wcr_status', '0');
                            break;
                        case 'ba-overdue':
                            $q->orWhereDate('ba_customer_commitment_date', '<=', $now);
                            break;
                        case 'customer-follow-up':
                            $q->orWhereDate('customer_follow_up_date', '<=', $now);
                            break;
                        case 'customer-follow-up-this-week':
                            $q->orWhereDate('customer_follow_up_date', '>=', $now)
                                ->whereDate('customer_follow_up_date', '<=', $endOfWeek);
                            break;
                        case 'customer-follow-up-tomorrow':
                            $q->orWhereDate('customer_follow_up_date', '=', $tomorrow);
                            break;
                        case 'payment-commited-on':
                            $q->orWhereDate('payment_commited_on', '<=', $now);
                            break;
                        case 'internal-follow-up':
                            $q->orWhereDate('internal_follow_up_date', '<=', $now);
                            break;
                    }
                }
            });
        }

        $query->orderBy($sortColumn, $sortOrder);

        return $query;
    }

    public function headings(): array
    {
        return [
            'S.No',
            'BDE Name',
            'Customer Name',
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


            // 'Doc Entry',
            // 'Branch ID',
            // 'BDE ID',
            // 'Order Type',
            //  'Customer Code',
            // 'Logistic WCR Status',
            //  'BDE Email ID',
            //  'Manager Email ID',
            //  'ART Email ID',
            //  'Logistic Email ID',
            //  'ART Head Email ID',
            // 'Default Customer Email',
            // 'Additional Emails',
            // 'WC Commitment Date',
            // 'Bill Accounted Status',
            // 'BA Customer Commitment Date',
            // 'Invoice Status',
            // 'Customer Follow up Date',
            // 'Payment Committed On',
            // 'Payment Revised Count',
            // 'Tough Nut Status',
            // 'RNR',
            // 'Internal Follow up Date',
            // 'Remarks email',
            // 'No of Tasks',
            // 'invoice link',

        ];
    }

    public function map($ialert): array
    {
        $this->serialNumber++;
        $attach = '=HYPERLINK("' . $ialert->sap_attachments . '")';
        $pdf = '=HYPERLINK("' . $ialert->invoice_pdf . '")';
        // $inv_link = '=HYPERLINK("' . config('app.task_url') . '/task?invoice_id=' . $ialert->id . '&tab=ialert")';
        $payment_revision_count = PaymentCommitedOn::where('ialert_id', $ialert->id)
            ->where(function ($query) {
                $query->whereNotNull('old_value')
                    ->whereNotNull('new_value');
            })
            ->count();
        return [
            $this->serialNumber,
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
            $ialert->invoice_status,
            $ialert->customer_follow_up_date ? \Carbon\Carbon::parse($ialert->customer_follow_up_date)->format('d-m-Y') : '',
            $ialert->payment_commited_on ? \Carbon\Carbon::parse($ialert->payment_commited_on)->format('d-m-Y') : '',
            $payment_revision_count > 0 ? $payment_revision_count : '',
            $ialert->tough_nut_status == '1' ? 'Yes' : ($ialert->tough_nut_status == '0' ? 'No' : ''),
            $ialert->rnr ? \Carbon\Carbon::parse($ialert->rnr)->format('d-m-Y') : '',
            $ialert->internal_follow_up_date ? \Carbon\Carbon::parse($ialert->internal_follow_up_date)->format('d-m-Y') : '',
            $ialert->remark_for_email ?? '',
            $ialert->tasks_count ?? 0,



            // $ialert->doc_entry ?? '-',
            //            $ialert->branch_id ?? '-',

            //            $ialert->bde_id ?? '-',

            //            $ialert->customer_code ?? '-',

            //            $ialert->bde_email_id ?? '-',
            //            $ialert->manager_email_id ?? '-',
            //            $ialert->art_email_id ?? '-',
            //            $ialert->logistics_email_id ?? '-',
            //            $ialert->art_head_email_id ?? '-',
            // $ialert->wc_date ? \Carbon\Carbon::parse($ialert->wc_date)->format('d-m-Y') : '-',
            // $ialert->bill_ac_status == '1' ? 'Yes' : ($ialert->bill_ac_status == '0' ? 'No' : '-'),
            // $ialert->ba_customer_commitment_date ? \Carbon\Carbon::parse($ialert->ba_customer_commitment_date)->format('d-m-Y') : '-',
            // $ialert->invoice_status,
            // $ialert->customer_follow_up_date ? \Carbon\Carbon::parse($ialert->customer_follow_up_date)->format('d-m-Y') : '-',
            // $ialert->payment_commited_on ? \Carbon\Carbon::parse($ialert->payment_commited_on)->format('d-m-Y') : '-',
            // $ialert->tough_nut_status == '1' ? 'Yes' : ($ialert->tough_nut_status == '0' ? 'No' : '-'),
            // $ialert->rnr ? \Carbon\Carbon::parse($ialert->rnr)->format('d-m-Y') : '-',
            // $ialert->internal_follow_up_date ? \Carbon\Carbon::parse($ialert->internal_follow_up_date)->format('d-m-Y') : '-',
            // $ialert->remark_for_email,
            // $inv_link ?? '-',
        ];
    }
}
