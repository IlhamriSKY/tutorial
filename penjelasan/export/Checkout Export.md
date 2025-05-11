## 📁 Lokasi File

`app/Http/Controllers/CheckoutPortController.php`

- - -

## 🔹 Fitur Import & Export Item via Excel (Untuk Checkout)

### 🔧 Function: `export()`

```php
public function export()
{
    return (new FastExcel($this->itemGenerator()))->download('items.xlsx');
}
```

#### 📝 Penjelasan:

*   Mengekspor seluruh data item ke Excel `items.xlsx`
*   Data diproses secara efisien menggunakan `yield` dari `itemGenerator()`

#### 📄 Struktur Kolom:

*   `name`, `code`, `sku`, `symbology`, `unit`, `categories`
*   `track_quantity`, `track_weight`, `has_serials`, `has_variants`
*   `alert_quantity`, `variants`

- - -

### 🔧 Function: `import()`

```php
public function import()
{
    return Inertia::render('Item/Import');
}
```

#### 📝 Penjelasan:

*   Menampilkan halaman upload Excel untuk item checkout

#### 📄 Vue Component:

`resources/js/Pages/Item/Import.vue`

- - -

### 🔧 Function: `save(Request $request)`

```php
public function save(Request $request)
{
    $request->validate(['excel' => 'required|file|mimes:xls,xlsx']);

    $items = (new FastExcel())->import(...);
}
```

#### 📝 Penjelasan:

*   Validasi file Excel (`.xls` atau `.xlsx`)
*   Proses baris demi baris untuk menyimpan/update data item
*   Sinkronisasi kategori berdasarkan kode
*   Variasi diolah jika `has_variants == yes`
*   Field penting: `name`, `code`, `categories`

#### 🧱 Contoh Format Excel:

| name  | code  | sku   | symbology | unit | categories | track\_quantity | has\_variants | variants               |
|-------|-------|-------|-----------|------|------------|----------------|--------------|------------------------|
| Kopi  | KP001 | SKU01 | code128   | PCS  | FOOD,DRINK | yes            | yes          | Rasa=Kopi,Latte|Size=S,M,L

#### 💥 Error Handling:

*   Jika field `name`, `code`, atau `categories` kosong → exception
*   Jika unit atau parent category tidak ditemukan → `sole()` akan error

- - -

### 🔧 Function: `itemGenerator()`

*   Digunakan saat ekspor item ke Excel
*   Memformat data dengan field dan struktur yang cocok untuk impor ulang

- - -

### 🔧 Function: `variantsToArray($variants)`

```js
Warna=Merah,Biru|Ukuran=S,M,L
```

*   Digunakan saat impor untuk mengkonversi string variasi menjadi array

- - -

### 🔧 Function: `variantsToText($variants)`

```js
[
  ['name' => 'Warna', 'option' => ['Merah', 'Biru']],
  ['name' => 'Ukuran', 'option' => ['S', 'M', 'L']]
]
```

*   Digunakan saat ekspor untuk mengkonversi array variasi menjadi string