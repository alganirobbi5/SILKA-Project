<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    public const JENIS_PEMASUKAN = 'pemasukan';
    public const JENIS_PENGELUARAN = 'pengeluaran';

    protected $fillable = [
        'tanggal', 'jenis', 'kategori_id', 'coa_id', 'nominal', 'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }

    public function scopeFilterTanggal($query, $tanggalAwal, $tanggalAkhir)
    {
        if ($tanggalAwal) {
            $query->where('tanggal', '>=', $tanggalAwal);
        }
        if ($tanggalAkhir) {
            $query->where('tanggal', '<=', $tanggalAkhir);
        }
    }

    public function scopeFilterJenis($query, $jenis)
    {
        if ($jenis) {
            $query->where('jenis', $jenis);
        }
    }

    public function scopeFilterKategori($query, $kategoriId)
    {
        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }
    }

    public function scopeFilterCoa($query, $coaId)
    {
        if ($coaId) {
            $query->where('coa_id', $coaId);
        }
    }

    public function scopeCariKeterangan($query, $keyword)
    {
        if ($keyword) {
            $query->where('keterangan', 'like', '%' . $keyword . '%');
        }
    }

    public function isPemasukan()
    {
        return $this->jenis === self::JENIS_PEMASUKAN;
    }

    public function getSignedEffectAttribute()
    {
        return $this->isPemasukan() ? (float) $this->nominal : -1 * (float) $this->nominal;
    }
}
