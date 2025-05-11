## 📁 Lokasi File

`app/Http/Controllers/ContactPortController.php`

- - -

## 🔹 Fitur Import & Export Kontak via Excel

### 🔧 Function: `export()`

```php
public function export()
{
    return (new FastExcel($this->contactGenerator()))->download('contacts.xlsx');
}
```

#### 📝 Penjelasan:

*   Ekspor seluruh data kontak ke file `contacts.xlsx`
*   Menggunakan `yield` dari `contactGenerator()` agar efisien

#### 📄 Format Kolom Excel:

| name         | email             | phone       | details         |
|--------------|-------------------|-------------|------------------|
| PT. Sumber   | sumber@mail.com   | 08121212    | pelanggan tetap |

- - -

### 🔧 Function: `import()`

```php
public function import()
{
    return Inertia::render('Contact/Import');
}
```

#### 📝 Penjelasan:

*   Menampilkan halaman unggah file kontak Excel

#### 📄 Vue Component:

`resources/js/Pages/Contact/Import.vue`

- - -

### 🔧 Function: `save(Request $request)`

```php
public function save(Request $request)
{
    $request->validate(['excel' => 'required|file|mimes:xls,xlsx']);

    $contacts = (new FastExcel())->import(...);
}
```

#### 📝 Penjelasan:

*   Validasi file Excel (`.xls`/`.xlsx`) wajib
*   Setiap baris minimal harus punya `name` dan `email` atau `phone`
*   Jika kontak sudah ada berdasarkan `name`, maka dilakukan update

#### 💥 Error Handling:

*   Jika `name` kosong, atau tidak ada `email` maupun `phone` → exception
*   Kesalahan ditangani dan dikembalikan ke halaman sebelumnya

- - -

### 🔧 Function: `contactGenerator()`

```php
private function contactGenerator()
{
    foreach (Contact::cursor() as $contact) {
        yield [
            'name'    => $contact->name,
            'email'   => $contact->email,
            'phone'   => $contact->phone,
            'details' => $contact->details,
        ];
    }
}
```

#### 📝 Penjelasan:

*   Digunakan oleh fungsi `export()` untuk membentuk data baris Excel
*   Menggunakan `cursor()` agar efisien untuk dataset besar