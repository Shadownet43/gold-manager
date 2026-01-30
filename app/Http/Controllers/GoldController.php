<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Setting;
use Carbon\Carbon;

class GoldController extends Controller
{
    // 1. TAMPILKAN HALAMAN UTAMA
    public function index()
    {
        return view('dashboard');
    }

    // 2. API: AMBIL DATA (READ)
    public function getData(Request $request)
    {
        $userId = auth()->id();
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');

        // Ambil Data Transaksi (hanya milik user)
        $transactions = Transaction::where('user_id', $userId)
                                   ->whereMonth('date', $month)
                                   ->whereYear('date', $year)
                                   ->orderByDesc('date')
                                   ->orderByDesc('time')
                                   ->get();

        // Hitung Statistik
        $income = $transactions->sum('total_rupiah');
        $goldSold = $transactions->sum('gold_amount');
        
        // Ambil Stok Gold (milik user)
        $stock = Setting::where('user_id', $userId)->where('key', 'gold_stock')->value('value') ?? 0;

        // Siapkan Data Grafik
        $chartData = Transaction::where('user_id', $userId)
                                ->whereMonth('date', $month)
                                ->whereYear('date', $year)
                                ->orderBy('date')
                                ->get()
                                ->groupBy('date')
                                ->map(function ($row) {
                                    return $row->sum('total_rupiah');
                                });

        // Format data transaksi dengan tanggal yang lebih readable
        $formattedTransactions = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'date' => Carbon::parse($transaction->date)->format('d-m-Y'),
                'date_raw' => $transaction->date->format('Y-m-d'),
                'time' => $transaction->time,
                'rate' => $transaction->rate,
                'gold_amount' => $transaction->gold_amount,
                'tax_status' => $transaction->tax_status,
                'total_rupiah' => $transaction->total_rupiah
            ];
        });

        // Tahun yang punya transaksi (untuk filter dinamis)
        $years = Transaction::where('user_id', $userId)
            ->selectRaw('DISTINCT YEAR(date) as year')
            ->orderBy('year')
            ->pluck('year');

        if ($years->isEmpty()) {
            $years = collect([(int) date('Y')]);
        }

        return response()->json([
            'data' => $formattedTransactions,
            'stats' => [
                'income' => $income,
                'gold_sold' => $goldSold,
                'stok_gold' => $stock
            ],
            'chart' => [
                'labels' => $chartData->keys()->map(fn($d) => Carbon::parse($d)->format('d')),
                'values' => $chartData->values()
            ],
            'years' => $years->values()->all()
        ]);
    }

    // 3. API: SIMPAN TRANSAKSI (JUAL)
    public function store(Request $request)
    {
        try {
            // Validasi Input
            $validated = $request->validate([
                'date' => 'required|date',
                'rate' => 'required|numeric|min:1',
                'gold_amount' => 'required|numeric|min:1',
                'tax_status' => 'nullable'
            ]);

            // Cek Stok Tersedia (milik user)
            $stock = Setting::where('user_id', auth()->id())->where('key', 'gold_stock')->value('value') ?? 0;
            if ($stock < $validated['gold_amount']) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $stock . ' g'
                ], 400);
            }

            // Hitung total
            $total = $validated['rate'] * $validated['gold_amount'];
            $isTax = $request->tax_status == 'on' ? 1 : 0; 
            if($isTax) $total *= 0.985;

            // Simpan ke Database (milik user)
            Transaction::create([
                'user_id' => auth()->id(),
                'date' => $validated['date'],
                'time' => now()->format('H:i:s'),
                'rate' => $validated['rate'],
                'gold_amount' => $validated['gold_amount'],
                'tax_status' => $isTax,
                'total_rupiah' => $total
            ]);

        // Kurangi Stok Otomatis (pastikan setting ada, milik user)
        $stockSetting = Setting::firstOrCreate(
            ['user_id' => auth()->id(), 'key' => 'gold_stock'],
            ['value' => 0]
        );
        $stockSetting->decrement('value', $validated['gold_amount']);

            return response()->json(['status' => 'success']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    // 4. API: HAPUS DATA
    public function destroy($id)
    {
        try {
            $trx = Transaction::where('user_id', auth()->id())->findOrFail($id);
            
            // Kembalikan Stok (Refund) sebelum dihapus
            $stockSetting = Setting::firstOrCreate(
                ['user_id' => auth()->id(), 'key' => 'gold_stock'],
                ['value' => 0]
            );
            $stockSetting->increment('value', $trx->gold_amount);
            
            $trx->delete();
            return response()->json(['status' => 'success']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    // 5. API: EDIT STOK TERAKHIR (set nilai stok, bukan tambah)
    public function updateStock(Request $request)
    {
        try {
            // Validasi: nilai stok terakhir (harus >= 0)
            $validated = $request->validate([
                'value' => 'required|numeric|min:0'
            ]);

            // Set nilai stok (bukan tambah, milik user)
            $setting = Setting::firstOrCreate(
                ['user_id' => auth()->id(), 'key' => 'gold_stock'],
                ['value' => 0]
            );

            $setting->update(['value' => $validated['value']]);

            return response()->json(['status' => 'success']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengubah stok: ' . $e->getMessage()
            ], 500);
        }
    }
}