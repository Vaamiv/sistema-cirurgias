<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surgery extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id','surgeon_name','start_at','end_at','status'
    ];
    protected $casts = [ 'start_at' => 'datetime', 'end_at' => 'datetime' ];

    public function patient() { return $this->belongsTo(Patient::class); }
}
