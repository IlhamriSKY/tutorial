## 📁 Lokasi File

`app/Http/Controllers/UnitPortController.php`

- - -

## 🔹 Fitur Import & Export Satuan (Unit) via Excel

### 🔧 Function: `export()`

```php
public function export()
{
    return (new FastExcel($this->unitGenerator()))->download('units.xlsx');
}
```

#### 📝 Penjelasan:

*   Mengekspor semua satuan ke file Excel `units.xlsx`
*   Format lengkap termasuk satuan dasar, operator, dan nilai konversi

#### 📄 Contoh Format Output Excel:

| name   | code | base\_unit | operator | operation\_value |
|--------|------|-----------|----------|-----------------|
| Liter  | LTR  |           |          |                 |
| mL     | ML   | LTR       | \*        | 0.001           |

- - -

### 🔧 Function: `import()`

```php
public function import()
{
    return Inertia::render('Unit/Import');
}
```

#### 📝 Penjelasan:

*   Menampilkan form unggah Excel untuk data unit

#### 📄 Vue Component:

`resources/js/Pages/Unit/Import.vue`

- - -

### 🔧 Function: `save(Request $request)`

```php
$units = (new FastExcel())->import(...);
```

#### 📝 Penjelasan:

*   Validasi file Excel wajib (xls/xlsx)
*   Field `name` dan `code` wajib
*   Jika ada `base_unit`, akan dicari berdasarkan `code` dan digunakan untuk relasi
*   Operator seperti `*` atau `/` digunakan untuk konversi antar satuan

#### 💥 Error Handling:

*   Jika `base_unit` tidak ditemukan → exception via `sole()`
*   Semua exception ditangkap dan dikirim kembali sebagai flash message

#### 🧪 Contoh Baris Excel Valid:

| name      | code | base\_unit | operator | operation\_value |
|-----------|------|-----------|----------|-----------------|
| Gram      | GR   |           |          |                 |
| Kilogram  | KG   | GR        | \*        | 1000            |

- - -

### 🔧 Function: `unitGenerator()`

```php
private function unitGenerator()
{
    foreach (Unit::cursor() as $unit) {
        yield [
            'name'            => $unit->name,
            'code'            => $unit->code,
            'base_unit'       => $unit->base_unit_id ? $unit->baseUnit->code : '',
            'operator'        => $unit->operator,
            'operation_value' => $unit->operation_value,
        ];
    }
}
```

#### 📝 Penjelasan:

*   Menghasilkan baris Excel satu per satu menggunakan `yield`
*   Memasukkan data konversi dan satuan dasar bila tersedia