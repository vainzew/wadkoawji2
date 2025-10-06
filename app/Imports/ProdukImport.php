<?php

namespace App\Imports;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ProdukImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithChunkReading, WithBatchInserts
{
    use SkipsFailures, SkipsErrors;

    public string $mode;
    public int $inserted = 0;
    public int $updated = 0;

    /** @var array<string,int> */
    protected array $kategoriCache = [];

    public function __construct(string $mode = 'upsert')
    {
        $this->mode = in_array($mode, ['insert','upsert']) ? $mode : 'upsert';
    }

    public function rules(): array
    {
        return [
            '*.nama_produk'   => ['required','string','max:255'],
            '*.nama_kategori' => ['required','string','max:255'],
            '*.harga_beli'    => ['required','numeric','min:0'],
            '*.harga_jual'    => ['required','numeric','min:0'],
            // pakai numeric agar format seperti "1.000" atau "1,000" tidak gagal
            '*.stok'          => ['required','numeric','min:0'],
            '*.expired_at'    => ['nullable','regex:/^\d{2}\/\d{2}$/'],
        ];
    }

    public function model(array $row)
    {
        // Normalisasi kategori (case-insensitive, rapikan spasi)
        $rawKategori = trim((string)($row['nama_kategori'] ?? ''));
        $cleanKategori = preg_replace('/\s+/', ' ', $rawKategori) ?: '';
        $key = mb_strtolower($cleanKategori);
        if ($key === '') {
            // biar SkipsOnFailure yang handle validasi required
            return null;
        }

        if (!isset($this->kategoriCache[$key])) {
            // Simpan dengan Title Case untuk tampilan rapi
            $display = mb_convert_case($cleanKategori, MB_CASE_TITLE, 'UTF-8');
            $kategori = Kategori::firstOrCreate(['nama_kategori' => $display]);
            $this->kategoriCache[$key] = (int)$kategori->id_kategori;
        }
        $idKategori = $this->kategoriCache[$key];

        // Unique key untuk upsert
        $barcode = isset($row['barcode']) ? (string)$row['barcode'] : null;
        $kode    = isset($row['kode_produk']) ? (string)$row['kode_produk'] : null;

        $data = [
            'nama_produk' => trim((string)($row['nama_produk'] ?? '')),
            'id_kategori' => $idKategori,
            'merk'        => trim((string)($row['merk'] ?? '')) ?: null,
            'harga_beli'  => (float)str_replace([',','.'], ['', '.'], (string)($row['harga_beli'] ?? 0)),
            'harga_jual'  => (float)str_replace([',','.'], ['', '.'], (string)($row['harga_jual'] ?? 0)),
            'stok'        => (int)floatval(str_replace([',','.'], ['', '.'], (string)($row['stok'] ?? 0))),
            'expired_at'  => trim((string)($row['expired_at'] ?? '')) ?: null,
            'diskon'      => (float)($row['diskon'] ?? 0),
            'barcode'     => $barcode ?: null,
        ];

        // Upsert logika
        $unique = $barcode ? ['barcode' => $barcode] : ($kode ? ['kode_produk' => $kode] : null);

        if ($unique && $this->mode === 'upsert') {
            $existing = Produk::where($unique)->first();
            if ($existing) {
                $existing->fill($data)->save();
                $this->updated++;
                return null;
            }
        } elseif ($unique) { // insert-only
            if (Produk::where($unique)->exists()) {
                return null; // duplikat, lewati
            }
        }

        // Generate kode_produk jika kosong saat insert
        if (!$kode) {
            $last = Produk::latest('id_produk')->first();
            $nextId = ($last->id_produk ?? 0) + 1;
            $data['kode_produk'] = 'P' . str_pad((string)$nextId, 6, '0', STR_PAD_LEFT);
        } else {
            $data['kode_produk'] = $kode;
        }

        $this->inserted++;
        return new Produk($data);
    }

    public function chunkSize(): int { return 1000; }
    public function batchSize(): int { return 500; }
}
