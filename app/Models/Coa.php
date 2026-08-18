<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    use HasFactory;

    protected $table = 'coa';

    protected $fillable = [
        'kode_coa', 'nama_coa', 'jenis', 'cluster', 'saldo',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
    ];

    public function clusterModel()
    {
        return $this->belongsTo(Cluster::class, 'cluster', 'id_cluster');
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'coa_id');
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_coa', 'like', '%' . $search . '%')
                    ->orWhere('nama_coa', 'like', '%' . $search . '%');
            });
        }
    }

    public function scopeByCluster($query, $clusterId)
    {
        if ($clusterId !== null && $clusterId !== '') {
            $query->where('cluster', $clusterId);
        }
    }
}
