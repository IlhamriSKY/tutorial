## 📁 Lokasi File

`app/Http/Controllers/StoragePortController.php`

- - -

## 🔹 Fitur Import & Export Storage via Excel

### 🔧 Function: `export()`

```php
public function export()
{
    return (new FastExcel($this->storageGenerator()))->download('storages.xlsx');
}
```

#### 📝 Penjelasan:

*   Mengekspor semua data `storage` ke file `storages.xlsx`
*   Data dihasilkan menggunakan `yield` melalui `storageGenerator()`

#### 📄 Contoh Format Output Excel:

| name       | description           | account\_id |
|------------|------------------------|------------|
| Gudang A   | Bahan baku utama       | 1          |
| Gudang B   | Produk jadi & packing  | 1          |

- - -

### 🔧 Function: `import()`

```php
public function import()
{
    return Inertia::render('Storage/Import');
}
```

#### 📝 Penjelasan:

*   Menampilkan form unggah file Excel untuk data storage

#### 📄 Vue Component:

`resources/js/Pages/Storage/Import.vue`

- - -

### 🔧 Function: `save(Request $request)`

```php
$request->validate([
    'excel' => 'required|file|mimes:xls,xlsx',
]);

$storages = (new FastExcel())->import(...);
```

#### 📝 Penjelasan:

*   Validasi file: wajib bertipe `.xls` atau `.xlsx`
*   Wajib menyertakan kolom `name`
*   Jika `description` atau `account_id` kosong, akan diberi nilai default
*   Gunakan `updateOrCreate()` berdasarkan `name` untuk menghindari duplikasi

#### 💥 Error Handling:

*   Jika `name` kosong, akan dilempar exception
*   Semua error ditangkap dan dikirim kembali ke form dengan pesan

- - -

### 🔧 Function: `storageGenerator()`

```php
private function storageGenerator()
{
    foreach (Storage::cursor() as $storage) {
        yield [
            'name'        => $storage->name,
            'description' => $storage->description,
            'account_id'  => $storage->account_id,
        ];
    }
}
```

#### 📝 Penjelasan:

*   Digunakan oleh `export()` untuk membentuk data Excel
*   Menggunakan `cursor()` agar efisien pada dataset besar