<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetCapaian extends Model
{
    use HasFactory;

    protected $table = 'target_capaians';

    protected $fillable = ['tahun', 'target_capaian'];

    protected $casts = [
        'target_capaian' => 'decimal:2',
    ];
}
