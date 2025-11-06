<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Surgery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'surgeon_name',
        'start_at',
        'end_at',
        'status',
        'responsible_assistant',
        'surgery_type',
        'procedure_type',
        'necessary_materials',
        'scheduled_by',
        'is_elective',
        'archive_reason',
    ];
    protected $casts = [ 'start_at' => 'datetime', 'end_at' => 'datetime' ];

    public function patient() { return $this->belongsTo(Patient::class); }
}
