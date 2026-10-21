<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $request->merge([
            'start_date' => $request->start_date ?? now()->startOfMonth()->toDateString(),
            'end_date' => $request->end_date ?? now()->toDateString(),
        ]);

        $data = $this->getData($request);
        $transaksis = $data['transaksis'];
        $totalPendapatan = $data['totalPendapatan'];

        return view('owner.laporan.index', compact('transaksis', 'totalPendapatan'));
    }

    protected function getData(Request $request)
    {
        $query = Transaksi::with(['kasir', 'pesanans']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transaksis = $query->latest()->get();
        $totalPendapatan = $transaksis->sum('total_harga');

        return compact('transaksis', 'totalPendapatan');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getData($request);
        $transaksis = $data['transaksis'];

        $filename = 'laporan-penjualan-' . now()->format('YmdHis') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($transaksis) {
            $file = fopen('php://output', 'w');

            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['No', 'Tanggal', 'Nomor Pesanan', 'Kasir', 'Tipe', 'Total', 'Bayar', 'Kembalian']);

            foreach ($transaksis as $index => $transaksi) {
                $nomorPesanan = $transaksi->pesanans->first()->nomor_pesanan ?? '-';
                $tipe = $transaksi->pesanans->first()->tipe_pesanan ?? '-';

                fputcsv($file, [
                    $index + 1,
                    $transaksi->created_at->format('d/m/Y H:i:s'),
                    $nomorPesanan,
                    $transaksi->kasir->name ?? '-',
                    $tipe,
                    number_format($transaksi->total_harga, 0, ',', '.'),
                    number_format($transaksi->bayar, 0, ',', '.'),
                    number_format($transaksi->kembalian, 0, ',', '.'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getData($request);
        $transaksis = $data['transaksis'];
        $totalPendapatan = $data['totalPendapatan'];
        $pengaturan = Pengaturan::first();

        $pdf = Pdf::loadView('owner.laporan.pdf', compact('transaksis', 'totalPendapatan', 'pengaturan'));
        return $pdf->download('laporan-penjualan-' . now()->format('YmdHis') . '.pdf');
    }

    public function cetak(Request $request)
    {
        $data = $this->getData($request);
        $transaksis = $data['transaksis'];
        $totalPendapatan = $data['totalPendapatan'];
        $pengaturan = Pengaturan::first();

        return view('owner.laporan.cetak', compact('transaksis', 'totalPendapatan', 'pengaturan'));
    }
}
