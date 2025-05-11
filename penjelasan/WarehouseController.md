## 📁 Lokasi File

`app/Http/Controllers/WarehouseController.php`

- - -

## 🔹 CRUD + Tampilan + Search + Query SQL

### 🔧 Function: `index(Request $request)`

```
public function index(Request $request)
{
    $filters = $request->all('search', 'trashed');

    return Inertia::render('Warehouse/Index', [
        'filters'    => $filters,
        'warehouses' => new WarehouseCollection(
            Warehouse::filter($filters)->orderByDesc('id')->paginate()->withQueryString()
        ),
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan daftar warehouse (gudang)
*   Support filter untuk pencarian dan data yang dihapus
*   Data dibungkus menggunakan `WarehouseCollection`

#### 💡 Contoh SQL Query (search = "pusat"):

```
SELECT * FROM warehouses
WHERE name LIKE '%pusat%'
ORDER BY id DESC
LIMIT 10 OFFSET 0;
```

#### 📄 Vue Component:

`resources/js/Pages/Warehouse/Index.vue`

- - -

### 🔧 Function: `create()`

```
public function create()
{
    return Inertia::render('Warehouse/Form');
}
```

#### 📝 Penjelasan:

*   Menampilkan form kosong untuk input data warehouse baru

#### 📄 Vue Component:

`resources/js/Pages/Warehouse/Form.vue`

- - -

### 🔧 Function: `store(WarehouseRequest $request)`

```
public function store(WarehouseRequest $request)
{
    Warehouse::create($request->validated());

    return redirect()->route('warehouses.index')
        ->with('message', __choice('action_text', ['record' => 'Warehouse', 'action' => 'created']));
}
```

#### 📝 Penjelasan:

*   Menambahkan data warehouse baru ke database
*   Validasi dilakukan oleh `WarehouseRequest`

#### 💡 Query SQL setara:

```
INSERT INTO warehouses (name, code, address, created_at, updated_at)
VALUES ('Gudang Pusat', 'GD-PST', 'Jl. Raya 1', NOW(), NOW());
```

- - -

### 🔧 Function: `edit(Warehouse $warehouse)`

```
public function edit(Warehouse $warehouse)
{
    return Inertia::render('Warehouse/Form', [
        'edit' => new WarehouseResource($warehouse),
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan form edit untuk warehouse tertentu
*   Data dikirim sebagai `WarehouseResource`

#### 📄 Vue Component:

`resources/js/Pages/Warehouse/Form.vue`

- - -

### 🔧 Function: `update(WarehouseRequest $request, Warehouse $warehouse)`

```
public function update(WarehouseRequest $request, Warehouse $warehouse)
{
    $warehouse->update($request->validated());

    return back()->with('message', __choice('action_text', ['record' => 'Warehouse', 'action' => 'updated']));
}
```

#### 📝 Penjelasan:

*   Memperbarui data warehouse berdasarkan ID

#### 💡 Query SQL setara:

```
UPDATE warehouses
SET name = 'Gudang Cabang', address = 'Jl. Baru 2', updated_at = NOW()
WHERE id = 7;
```

- - -

### 🔧 Function: `destroy(Warehouse $warehouse)`

```
public function destroy(Warehouse $warehouse)
{
    if ($warehouse->del()) {
        return redirect()->route('warehouses.index')
            ->with('message', __choice('action_text', ['record' => 'Warehouse', 'action' => 'deleted']));
    }

    return back()->with('error', __('The record can not be deleted.'));
}
```

#### 📝 Penjelasan:

*   Melakukan soft delete terhadap warehouse
*   Menggunakan method `del()` dari model

#### 💡 Query SQL setara:

```
UPDATE warehouses
SET deleted_at = NOW()
WHERE id = 7;
```

- - -

### 🔧 Function: `destroyPermanently(Warehouse $warehouse)`

```
public function destroyPermanently(Warehouse $warehouse)
{
    if ($warehouse->delP()) {
        return redirect()->route('warehouses.index')
            ->with('message', __choice('action_text', ['record' => 'Warehouse', 'action' => 'permanently deleted']));
    }

    return back()->with('error', __('The record can not be deleted.'));
}
```

#### 📝 Penjelasan:

*   Hapus data warehouse secara permanen (hard delete)
*   Menggunakan method `delP()` dari model

#### 💡 Query SQL setara:

```
DELETE FROM warehouses
WHERE id = 7;
```

- - -

### 🔧 Function: `restore(Warehouse $warehouse)`

```
public function restore(Warehouse $warehouse)
{
    $warehouse->restore();
    $warehouse->stock()->restore();

    return back()->with('message', __choice('action_text', ['record' => 'Warehouse', 'action' => 'restored']));
}
```

#### 📝 Penjelasan:

*   Mengembalikan warehouse dan relasi `stock()` yang soft-deleted
*   Pastikan relasi `stock()` menggunakan `SoftDeletes` juga

#### 💡 Query SQL setara:

```
UPDATE warehouses SET deleted_at = NULL WHERE id = 7;
UPDATE stocks SET deleted_at = NULL WHERE warehouse_id = 7;
```