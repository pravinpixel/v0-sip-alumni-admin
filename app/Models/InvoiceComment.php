<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceComment extends Model
{
    use HasFactory;

    protected $table = 'invoice_comments';

    public function from() {
        return $this->hasOne(Employee::class, 'id', 'from_id')->select('id', 'first_name', 'last_name','profile_image')->selectRaw("CONCAT(first_name, ' ', last_name) as name");
    }

    public function documents() {
        return $this->hasMany(InvoiceCommentDocument::class, 'comment_id', 'id')->select('id', 'comment_id', 'document','name');
    }
}
