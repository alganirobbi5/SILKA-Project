<?php

namespace App\Services;

use App\Models\Coa;
use App\Models\Transaksi;
use Exception;
use Illuminate\Support\Facades\DB;

class TransaksiSaldoService
{
    /**
     * Efek bertanda dari sebuah transaksi terhadap saldo COA.
     * pemasukan = +nominal, pengeluaran = -nominal (PRD TRX-004).
     */
    public function effect(string $jenis, $nominal)
    {
        $value = (float) $nominal;
        return $jenis === Transaksi::JENIS_PEMASUKAN ? $value : -1 * $value;
    }

    /**
     * Buat transaksi baru dan terapkan efek ke saldo COA secara atomik.
     *
     * @throws Exception
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $coa = Coa::whereKey($data['coa_id'])->lockForUpdate()->first();
            if (!$coa) {
                throw new Exception('Akun COA tidak ditemukan.');
            }

            $transaksi = Transaksi::create($data);

            $coa->saldo = (float) $coa->saldo + $this->effect($data['jenis'], $data['nominal']);
            $coa->save();

            return $transaksi;
        }, 3);
    }

    /**
     * Update transaksi dan sesuaikan saldo COA secara atomik.
     * Revert efek lama lalu terapkan efek baru (PRD TRX-006).
     *
     * @throws Exception
     */
    public function update(Transaksi $transaksi, array $data)
    {
        return DB::transaction(function () use ($transaksi, $data) {
            // Kunci record transaksi lama
            $locked = Transaksi::whereKey($transaksi->id)->lockForUpdate()->first();
            if (!$locked) {
                throw new Exception('Transaksi tidak ditemukan.');
            }

            $oldCoaId = $locked->coa_id;
            $newCoaId = $data['coa_id'];
            $oldJenis = $locked->jenis;
            $oldNominal = $locked->nominal;

            // Kunci COA terdampak dalam urutan ID ascending (PRD 11.3)
            $coaIds = array_unique(array_filter([$oldCoaId, $newCoaId]));
            sort($coaIds);
            $coas = Coa::whereIn('id', $coaIds)->lockForUpdate()->get()->keyBy('id');

            if (!isset($coas[$newCoaId])) {
                throw new Exception('Akun COA tujuan tidak ditemukan.');
            }
            if ($oldCoaId && !isset($coas[$oldCoaId])) {
                throw new Exception('Akun COA asal tidak ditemukan.');
            }

            // Revert efek lama
            if ($oldCoaId && isset($coas[$oldCoaId])) {
                $coas[$oldCoaId]->saldo = (float) $coas[$oldCoaId]->saldo - $this->effect($oldJenis, $oldNominal);
                $coas[$oldCoaId]->save();
            }

            // Terapkan efek baru
            $coas[$newCoaId]->saldo = (float) $coas[$newCoaId]->saldo + $this->effect($data['jenis'], $data['nominal']);
            $coas[$newCoaId]->save();

            $locked->fill($data);
            $locked->save();

            return $locked;
        }, 3);
    }

    /**
     * Hapus transaksi dan revert saldo COA secara atomik (PRD TRX-007).
     *
     * @throws Exception
     */
    public function delete(Transaksi $transaksi)
    {
        DB::transaction(function () use ($transaksi) {
            $locked = Transaksi::whereKey($transaksi->id)->lockForUpdate()->first();
            if (!$locked) {
                throw new Exception('Transaksi tidak ditemukan.');
            }

            if ($locked->coa_id) {
                $coa = Coa::whereKey($locked->coa_id)->lockForUpdate()->first();
                if (!$coa) {
                    throw new Exception('Akun COA tidak ditemukan.');
                }

                $coa->saldo = (float) $coa->saldo - $this->effect($locked->jenis, $locked->nominal);
                $coa->save();
            }

            $locked->delete();
        }, 3);
    }
}
