## 📁 Lokasi File

`app/Http/Controllers/StorageController.php`

- - -

## 🔹 CRUD + Tampilan + Search + Query SQL

### 🔧 Function: `index(Request $request)`

```
public function index(Request $request)
{
    $filters = $request->all('search', 'trashed');

    return Inertia::render('Storage/Index', [
        'filters'  => $filters,
        'storages' => new StorageCollection(
            Storage::filter($filters)->orderByDesc('id')->paginate()->withQueryString()
        ),
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan daftar storage/gudang
*   Filter pencarian dan soft-delete ditangani oleh `filter()` di model

#### 💡 Contoh Query SQL:

```
SELECT * FROM storages
WHERE name LIKE '%gudang%'
ORDER BY id DESC
LIMIT 10 OFFSET 0;
```

#### 📄 Vue Component:

`resources/js/Pages/Storage/Index.vue`

- - -

### 🔧 Function: `create()`

```
public function create()
{
    return Inertia::render('Storage/Form');
}
```

#### 📝 Penjelasan:

*   Menampilkan form kosong untuk tambah data storage baru

#### 📄 Vue Component:

`resources/js/Pages/Storage/Form.vue`

- - -

### 🔧 Function: `store(StorageRequest $request)`

```
public function store(StorageRequest $request)
{
    Storage::create($request->validated());

    return redirect()->route('storages.index')
        ->with('message', __choice('action_text', ['record' => 'Storage', 'action' => 'created']));
}
```

#### 📝 Penjelasan:

*   Menyimpan data gudang ke database
*   Validasi data menggunakan `StorageRequest`

#### 💡 Query SQL setara:

```
INSERT INTO storages (name, code, location, created_at, updated_at)
VALUES ('Gudang A', 'GDG-A', 'Jakarta', NOW(), NOW());
```

- - -

### 🔧 Function: `edit(Storage $storage)`

```
public function edit(Storage $storage)
{
    return Inertia::render('Storage/Form', [
        'edit' => new StorageResource($storage),
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan form edit untuk data storage tertentu
*   Data disiapkan sebagai resource agar mudah dipakai di frontend

#### 📄 Vue Component:

`resources/js/Pages/Storage/Form.vue`

- - -

### 🔧 Function: `update(StorageRequest $request, Storage $storage)`

```
public function update(StorageRequest $request, Storage $storage)
{
    $storage->update($request->validated());

    return back()->with('message', __choice('action_text', ['record' => 'Storage', 'action' => 'updated']));
}
```

#### 📝 Penjelasan:

*   Melakukan update terhadap data gudang yang ada

#### 💡 Query SQL setara:

```
UPDATE storages
SET name = 'Gudang B', location = 'Bandung', updated_at = NOW()
WHERE id = 3;
```

- - -

### 🔧 Function: `destroy(Storage $storage)`

```
public function destroy(Storage $storage)
{
    if ($storage->del()) {
        return redirect()->route('storages.index')
            ->with('message', __choice('action_text', ['record' => 'Storage', 'action' => 'deleted']));
    }

    return back()->with('error', __('The record can not be deleted.'));
}
```

#### 📝 Penjelasan:

*   Soft delete (menandai data sebagai dihapus tanpa menghapus secara permanen)
*   Method `del()` adalah fungsi kustom di model

#### 💡 Query SQL setara:

```
UPDATE storages
SET deleted_at = NOW()
WHERE id = 3;
```

- - -

### 🔧 Function: `destroyPermanently(Storage $storage)`

```
public function destroyPermanently(Storage $storage)
{
    if ($storage->delP()) {
        return redirect()->route('storages.index')
            ->with('message', __choice('action_text', ['record' => 'Storage', 'action' => 'permanently deleted']));
    }

    return back()->with('error', __('The record can not be deleted.'));
}
```

#### 📝 Penjelasan:

*   Hapus permanen dari database menggunakan `delP()`

#### 💡 Query SQL setara:

```
DELETE FROM storages
WHERE id = 3;
```

- - -

### 🔧 Function: `restore(Storage $storage)`

```
public function restore(Storage $storage)
{
    $storage->restore();

    return back()->with('message', __choice('action_text', ['record' => 'Storage', 'action' => 'restored']));
}
```

#### 📝 Penjelasan:

*   Mengembalikan data soft deleted (restore)

#### 💡 Query SQL setara:

```
UPDATE storages
SET deleted_at = NULL
WHERE id = 3;
```