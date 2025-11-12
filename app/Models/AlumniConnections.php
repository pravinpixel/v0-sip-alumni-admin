<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumniConnections extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'alumni_connections';
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status'
    ];

    public function sender()
    {
        return $this->belongsTo(Alumnis::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Alumnis::class, 'receiver_id');
    }

}