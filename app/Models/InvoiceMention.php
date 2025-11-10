<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceMention extends Model
{
    use HasFactory;

    protected $table = 'invoice_mentions';

    public function mentionedEmployee()
    {
        return $this->belongsTo(Employee::class, 'mentioned_id', 'id');
    }

    public function mentionedBy()
    {
        return $this->belongsTo(Employee::class, 'mentioned_by', 'id');
    }

    public function mentionedInvoice()
    {
        return $this->belongsTo(Iallert::class, 'invoice_id', 'id');
    }
}
