## 📁 Lokasi File

`app/Http/Controllers/CheckinPortController.php`

- - -

## 🔹 Fitur Import & Export Item via Excel

### 🔧 Function: `export()`

```php
public function export()
{
    return (new FastExcel($this->itemGenerator()))->download('items.xlsx');
}
```

#### 📝 Penjelasan:

*   Mengekspor seluruh data item ke file Excel `items.xlsx`
*   Data diekspor menggunakan `yield` dari method `itemGenerator()`

#### 📄 Format Kolom Output:

*   `name`, `code`, `sku`, `symbology`
*   `unit`: kode unit
*   `categories`: kode kategori dipisah koma
*   `track_quantity`, `track_weight`, `has_serials`, `has_variants`: 'yes' atau kosong
*   `variants`: format seperti `Warna=Merah,Biru|Ukuran=S,M,L`

- - -

### 🔧 Function: `import()`

```php
public function import()
{
    return Inertia::render('Item/Import');
}
```

#### 📝 Penjelasan:

*   Menampilkan form unggah file Excel

#### 📄 Vue Component:

`resources/js/Pages/Item/Import.vue`

- - -

### 🔧 Function: `save(Request $request)`

```php
public function save(Request $request)
{
    $request->validate(['excel' => 'required|file|mimes:xls,xlsx']);

    $items = (new FastExcel())->import(Storage::path($path), function ($line) use ($symbologies) {
        ...
        return $item;
    });
}
```

#### 📝 Penjelasan:

*   Validasi file wajib format `.xls` atau `.xlsx`
*   Setiap baris item:
    *   `name`, `code`, dan `categories` wajib
    *   Unit dicari berdasarkan `code` → `Unit::where(...)->sole()`
    *   Variasi dikonversi dari teks ke array dengan `variantsToArray()`
    *   Sinkronisasi kategori berdasarkan kode
*   Jika error, pesan ditampilkan via flash message

#### 📦 Contoh Struktur Excel yang Valid:

| name    | code   | sku     | symbology | unit | categories | track\_quantity | has\_variants | variants               |
|---------|--------|---------|-----------|------|------------|----------------|--------------|------------------------|
| Beras   | BR001  | SKU001  | code128   | KG   | FOODS      | yes            | yes          | Warna=Putih,Coklat     |

- - -

### 🔧 Function: `itemGenerator()`

*   Menghasilkan data item satu per satu dalam format array
*   Efisien untuk ekspor dalam jumlah besar

- - -

### 🔧 Function: `variantsToArray($variants)`

*   Contoh input: `Warna=Merah,Biru|Ukuran=S,M,L`
*   Output: array untuk fungsi `addVariations()`

```js
[
  ['name' => 'Warna', 'option' => ['Merah', 'Biru']],
  ['name' => 'Ukuran', 'option' => ['S', 'M', 'L']],
]
```

- - -

### 🔧 Function: `variantsToText($variants)`

*   Kebalikan dari `variantsToArray()`
*   Digunakan saat ekspor item

```js
Warna=Merah,Biru|Ukuran=S,M,L
```