## 📁 Lokasi File

`app/Http/Controllers/WarehousePortController.php`

- - -

## 🔹 Fitur Import & Export Data Gudang (Warehouse) via Excel

### 🔧 Function: `export()`

```php
public function export()
{
    return (new FastExcel($this->warehouseGenerator()))->download('warehouses.xlsx');
}
```

#### 📝 Penjelasan:

*   Ekspor seluruh data gudang ke file `warehouses.xlsx`
*   Setiap baris berisi info lengkap gudang termasuk status aktif

#### 📄 Contoh Format Output Excel:

| code | name     | email             | phone       | address           | active |
|------|----------|-------------------|-------------|-------------------|--------|
| WH01 | Gudang A | gudangA@mail.com  | 08121212121 | Jl. Gudang No. 1  | 1      |
| WH02 | Gudang B |                   |             |                   | 0      |

- - -

### 🔧 Function: `import()`

```php
public function import()
{
    return Inertia::render('Warehouse/Import');
}
```

#### 📝 Penjelasan:

*   Menampilkan form unggah file Excel untuk data gudang

#### 📄 Vue Component:

`resources/js/Pages/Warehouse/Import.vue`

- - -

### 🔧 Function: `save(Request $request)`

```php
$warehouses = (new FastExcel())->import(...);
```

#### 📝 Penjelasan:

*   Validasi file wajib bertipe Excel (`.xls` / `.xlsx`)
*   `name` dan `code` adalah kolom wajib
*   Jika `active` = `yes` maka `true`, selain itu `false`
*   Gunakan `updateOrCreate()` berdasarkan `name` (bisa disesuaikan ke `code` jika dibutuhkan)

#### 💥 Validasi & Error:

*   Jika kolom wajib kosong, akan dilempar sebagai exception dan ditangani dengan flash message

- - -

### 🔧 Function: `warehouseGenerator()`

```php
private function warehouseGenerator()
{
    foreach (Warehouse::cursor() as $warehouse) {
        yield [
            'code'    => $warehouse->code,
            'name'    => $warehouse->name,
            'email'   => $warehouse->email,
            'phone'   => $warehouse->phone,
            'address' => $warehouse->address,
            'active'  => $warehouse->active,
        ];
    }
}
```

#### 📝 Penjelasan:

*   Digunakan saat ekspor gudang ke Excel
*   Setiap baris mencakup detail gudang dan status aktif (boolean)