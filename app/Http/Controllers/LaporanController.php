<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportFilterRequest;
use App\Models\Kategori;
use App\Services\ReportService;
use App\Support\PdfRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LaporanController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(ReportFilterRequest $request)
    {
        $kategoris = $this->reportService->kategoris();

        $filters = [
            'kategori_id' => $request->kategori_id,
            'jenis' => $request->jenis,
            'tanggal_awal' => $request->tanggal_awal,
            'tanggal_akhir' => $request->tanggal_akhir,
        ];

        $hasFilter = $request->filled('kategori_id') || $request->filled('jenis') ||
            $request->filled('tanggal_awal') || $request->filled('tanggal_akhir');

        $transaksis = $hasFilter ? $this->reportService->query($filters)->paginate(50)->withQueryString() : null;
        $totals = $hasFilter ? $this->reportService->totals($filters) : null;

        return view('laporan.index', compact('kategoris', 'transaksis', 'totals', 'filters', 'hasFilter'));
    }

    public function print(ReportFilterRequest $request)
    {
        $filters = [
            'kategori_id' => $request->kategori_id,
            'jenis' => $request->jenis,
            'tanggal_awal' => $request->tanggal_awal,
            'tanggal_akhir' => $request->tanggal_akhir,
        ];

        $hasFilter = $request->filled('kategori_id') || $request->filled('jenis') ||
            $request->filled('tanggal_awal') || $request->filled('tanggal_akhir');

        $transaksis = $hasFilter ? $this->reportService->data($filters) : collect();
        $totals = $hasFilter ? $this->reportService->totals($filters) : null;
        $kategori = $filters['kategori_id'] ? Kategori::find($filters['kategori_id']) : null;

        return view('laporan.print', compact('transaksis', 'totals', 'filters', 'kategori', 'hasFilter'));
    }

    public function pdf(ReportFilterRequest $request)
    {
        $filters = [
            'kategori_id' => $request->kategori_id,
            'jenis' => $request->jenis,
            'tanggal_awal' => $request->tanggal_awal,
            'tanggal_akhir' => $request->tanggal_akhir,
        ];

        $transaksis = $this->reportService->data($filters);
        $totals = $this->reportService->totals($filters);
        $kategori = $filters['kategori_id'] ? Kategori::find($filters['kategori_id']) : null;

        $html = view('laporan.pdf', compact('transaksis', 'totals', 'filters', 'kategori'))->render();

        return PdfRenderer::render($html, 'laporan-transaksi-' . date('Y-m-d') . '.pdf');
    }

    public function export(ReportFilterRequest $request)
    {
        $filters = [
            'kategori_id' => $request->kategori_id,
            'jenis' => $request->jenis,
            'tanggal_awal' => $request->tanggal_awal,
            'tanggal_akhir' => $request->tanggal_akhir,
        ];

        $transaksis = $this->reportService->data($filters);
        $totals = $this->reportService->totals($filters);

        $filename = 'laporan-transaksi-' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($transaksis, $totals) {
            echo \App\Exports\TransaksiReportExport::build($transaksis, $totals);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
