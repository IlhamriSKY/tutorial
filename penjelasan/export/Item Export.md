## 📁 Lokasi File

`app/Http/Controllers/ItemPortController.php`

- - -

## 🔹 Fitur Export & Import Item via Excel (Dengan Stok per Gudang)

### 🔧 Function: `export(Request $request)`

```php
public function export(Request $request)
{
    $filters = $request->all(['search', 'category']);

    return (new FastExcel($this->itemGenerator($filters)))->download('items.xlsx');
}
```

#### 📝 Penjelasan:

*   Mengekspor item ke file Excel
*   Mendukung filter: `search` dan `category`
*   Stok diekspor dalam bentuk total dan stok per warehouse

#### 📄 Contoh Format Kolom Excel Output:

| name | code | unit | categories | alert\_quantity | quantity | GUDANG\_A | GUDANG\_B | ... |

- - -

### 🔧 Function: `import()`

```php
public function import()
{
    return Inertia::render('Item/Import');
}
```

#### 📝 Penjelasan:

*   Menampilkan form untuk unggah file Excel item

- - -

### 🔧 Function: `save(Request $request)`

```php
$items = (new FastExcel())->import(...);
```

#### 📝 Penjelasan:

*   Validasi file Excel wajib `.xls` atau `.xlsx`
*   Setiap baris item divalidasi untuk `name`, `code`, `categories`
*   Fitur tambahan yang didukung:
    *   `rack_location`, `photo` (dalam folder `items/`)
    *   `has_variants` + `variants`
    *   `unit` dan `storage` berdasarkan `code`
    *   Jika item baru → jalankan `setStock()`

#### 💥 Validasi dan Error:

*   Jika parent kategori/unit tidak ditemukan → akan error via `sole()`
*   Semua exception dikembalikan ke halaman sebelumnya dengan flash message

#### 📦 Contoh Baris Excel Valid:

| name   | code  | unit | categories | has\_variants | variants             | photo         |
|--------|-------|------|------------|--------------|----------------------|---------------|
| Gula   | GL001 | KG   | BAHAN      | yes          | Jenis=Putih,Coklat   | gula.jpg      |

- - -

### 🔧 Function: `itemGenerator($filters)`

*   Mengembalikan data item satu per satu menggunakan `yield`
*   Mendukung filter dan ekspor stok berdasarkan warehouse
*   Menambahkan total kuantitas dan stok per gudang secara dinamis

#### 📄 Contoh Output Jika Ada 2 Gudang:

| name | code | quantity | GUDANG\_A | GUDANG\_B |
|------|------|----------|----------|----------|
| Gula | GL01 | 100      | 60       | 40       |

- - -

### 🔧 Function: `variantsToArray($variants)`

*   Input: `Warna=Merah,Biru|Ukuran=S,M,L`
*   Output: array untuk disimpan sebagai variasi

- - -

### 🔧 Function: `variantsToText($variants)`

*   Mengubah array variasi menjadi string yang bisa diekspor
*   Kebalikan dari `variantsToArray()`

```js
[
  ['name' => 'Warna', 'option' => ['Merah', 'Biru']]
] → Warna=Merah,Biru
```