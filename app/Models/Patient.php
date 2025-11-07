<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    protected $fillable = ['name','document','birth_date','contact'];
    protected $casts = [
        'birth_date' => 'date',
    ];
    public function surgeries() { return $this->hasMany(Surgery::class); }
}
