<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cluster extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'cluster';

    protected $primaryKey = 'id_cluster';

    protected $fillable = ['nama'];
}
