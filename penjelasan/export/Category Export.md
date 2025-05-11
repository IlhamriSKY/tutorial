## 📁 Lokasi File

`app/Http/Controllers/CategoryPortController.php`

- - -

## 🔹 Fitur Import & Export Kategori via Excel

### 🔧 Function: `export()`

```php
public function export()
{
    return (new FastExcel($this->categoryGenerator()))->download('categories.xlsx');
}
```

#### 📝 Penjelasan:

*   Mengekspor seluruh data kategori ke file `categories.xlsx`
*   Data diambil menggunakan `yield` untuk efisiensi memori (lazy loading)

#### 📄 Format Kolom Excel:

*   `name`: Nama kategori
*   `code`: Kode unik
*   `parent`: Kode kategori induk (jika ada)

- - -

### 🔧 Function: `import()`

```php
public function import()
{
    return Inertia::render('Category/Import');
}
```

#### 📝 Penjelasan:

*   Menampilkan halaman untuk mengunggah file Excel kategori

#### 📄 Vue Component:

`resources/js/Pages/Category/Import.vue`

- - -

### 🔧 Function: `save(Request $request)`

```php
public function save(Request $request)
{
    $request->validate(['excel' => 'required|file|mimes:xls,xlsx']);

    $path = $request->file('excel')->store('imports');

    try {
        $categories = (new FastExcel())->import(Storage::path($path), function ($line) {
            if (! $line['name'] || ! $line['code']) {
                throw new \Exception(__('name & code are required.'));
            }

            return Category::updateOrCreate(['code' => $line['code']], [
                'name'      => $line['name'],
                'parent_id' => $line['parent'] ? Category::where('code', $line['parent'])->sole()->id : null,
            ]);
        });
    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }

    return redirect()->route('categories.index')->with('message', __choice('imported_text', ['records' => 'Category', 'count' => $categories->count()]));
}
```

#### 📝 Penjelasan:

*   Validasi file wajib bertipe Excel (`.xls` atau `.xlsx`)
*   File disimpan sementara di storage `/imports`
*   Setiap baris dieksekusi:

*   Wajib memiliki `name` dan `code`
*   `parent` akan dicari berdasarkan kode (harus unik)

*   Jika `code` sudah ada, akan dilakukan `update`; jika belum, akan dibuat baru

#### 📦 Contoh Struktur Excel yang Valid:

| name          | code   | parent |
|---------------|--------|--------|
| Barang Elektronik | E01    |        |
| Laptop        | E02    | E01    |
| Aksesoris     | E03    | E01    |

#### 💥 Error Handling:

*   Jika `parent` tidak ditemukan atau duplikat, `where(...)->sole()` akan melempar exception
*   Semua error dikembalikan ke halaman sebelumnya dengan flash message

- - -

### 🔧 Function: `categoryGenerator()`

```php
private function categoryGenerator()
{
    foreach (Category::cursor() as $category) {
        yield [
            'name'   => $category->name,
            'code'   => $category->code,
            'parent' => $category->parent_id ? $category->parent->code : '',
        ];
    }
}
```

#### 📝 Penjelasan:

*   Digunakan oleh fungsi `export()` untuk membangun baris Excel
*   Efisien karena tidak memuat semua data sekaligus ke memori