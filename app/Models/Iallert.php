<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iallert extends Model
{
    use HasFactory;

    protected $table ='i_alerts';

    protected $fillable = [
        'doc_entry',
        'branch_id',
        'branch',
        'bde_id',
        'bde_name',
        'order_type',
        'invoice_number',
        'invoice_date',
        'einvoice_number',
        'customer_code',
        'customer_name',
        'po_reference',
        'payment_terms',
        'balance_remarks',
        'contact_person',
        'mobile',
        'email_id',
        'logistic_wcr_status',
        'portal_invoice',
        'sap_attachments',
        'invoice_pdf',
        'invoice_value',
        'os_value',
        'age',
        'bde_email_id',
        'manager_email_id',
        'art_email_id',
        'logistics_email_id',
        'art_head_email_id'
    ];

    public function organization()
    {
        return $this->hasMany(Organization::class, 'customer_code' , 'customer_code');
    }

    public function documents()
    {
        return $this->hasMany(InvoiceDocument::class, 'invoice_id', 'id')->select('id', 'invoice_id', 'document','name');
    }

    public function comments()
    {
        return $this->hasMany(InvoiceComment::class, 'invoice_id', 'id')->orderBy('created_at', 'desc');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'ialert_id', 'id')
            ->withTrashed()
            ->with(['assignedto', 'assignedby'])
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('is_recurrence', 0)
                        ->whereNull('parent_id');
                })->orWhere(function ($query) {
                    $query->whereNotNull('parent_id')
                        ->where('is_recurrence', 1);
                });
            });
    }
}
