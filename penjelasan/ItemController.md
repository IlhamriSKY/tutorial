## 📁 Lokasi File

`app/Http/Controllers/ItemController.php`

- - -

## 🔹 CRUD + Tampilan + Stock Trail + Query SQL

### 🔧 Function: `index(Request $request)`

```
public function index(Request $request)
{
    $filters = $request->only(['search', 'trashed', 'category']);

    $items = Item::with(['categories', 'storage', 'stock.warehouse'])
        ->filter($filters)
        ->orderByDesc('id')
        ->paginate()
        ->withQueryString();

    foreach ($items as $item) {
        if ($item->storage) {
            $item->storage->remaining_capacity = StorageHelper::getRemainingCapacity($item->storage->id);
        }
    }

    return Inertia::render('Item/Index', [
        'filters'    => $filters,
        'warehouses' => Warehouse::ofAccount()->active()->get(),
        'items'      => new ItemCollection($items),
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan daftar item dengan filter pencarian, kategori, dan soft delete
*   Menambahkan data kapasitas gudang yang tersisa

#### 📄 Vue Component:

`resources/js/Pages/Item/Index.vue`

- - -

### 🔧 Function: `create()`

*   Menampilkan form tambah barang baru

#### 📄 Vue Component:

`resources/js/Pages/Item/Form.vue`

- - -

### 🔧 Function: `store(ItemRequest $request)`

```
public function store(ItemRequest $request)
{
    $data = $request->validated();
    Item::create($data)->addVariations()->saveRelations($data);

    return redirect()->route('items.index')->with('message', __choice('action_text', ['record' => 'Item', 'action' => 'created']));
}
```

#### 📝 Penjelasan:

*   Menyimpan item baru dan relasi variasinya

#### 💡 Contoh SQL:

```
INSERT INTO items (name, code, unit_id, created_at, updated_at)
VALUES ('Beras', 'BR001', 2, NOW(), NOW());
```

- - -

### 🔧 Function: `edit(Item $item)`

*   Menampilkan form edit item, beserta kategori dan stok

#### 📄 Vue Component:

`resources/js/Pages/Item/Form.vue`

- - -

### 🔧 Function: `update(ItemRequest $request, Item $item)`

```
public function update(ItemRequest $request, Item $item)
{
    $data = $request->validated();
    $item->update($data);
    $item->addVariations()->saveRelations($data);
    session()->flash('message', __choice('action_text', ['record' => 'Item', 'action' => 'updated']));

    return $request->listing == 'yes' ? redirect()->route('items.index') : back();
}
```

#### 📝 Penjelasan:

*   Memperbarui data item dan menyimpan relasi variasinya

- - -

### 🔧 Function: `show(Request $request, Item $item)`

*   Menampilkan detail item beserta stok di seluruh warehouse
*   Menggunakan parameter `$request->json` untuk menentukan format response

#### 📄 Vue Component (mode visual):

`resources/js/Pages/Item/Show.vue`

- - -

### 🔧 Function: `destroy(Item $item)`

*   Soft delete item

#### 💡 Query SQL:

```
UPDATE items SET deleted_at = NOW() WHERE id = 1;
```

- - -

### 🔧 Function: `destroyPermanently(Item $item)`

*   Menghapus item dari database secara permanen

#### 💡 Query SQL:

```
DELETE FROM items WHERE id = 1;
```

- - -

### 🔧 Function: `destroyPhoto(Item $item)`

```
public function destroyPhoto(Item $item)
{
    if (FileStorage::disk('assets')->delete($item->photo)) {
        $item->update(['photo' => null]);

        return back()->with('message', __choice('action_text', ['record' => 'Item photo', 'action' => 'deleted']));
    }

    return back()->with('error', __('The record can not be deleted.'));
}
```

#### 📝 Penjelasan:

*   Menghapus file foto barang dari penyimpanan dan mengosongkan kolom `photo`

- - -

### 🔧 Function: `restore(Item $item)`

*   Mengembalikan item yang sudah soft deleted

#### 💡 Query SQL:

```
UPDATE items SET deleted_at = NULL WHERE id = 1;
```

- - -

### 🔧 Function: `trail(Item $item)`

```
public function trail(Item $item)
{
    $item->load('stockTrails');

    return Inertia::render('Item/Trail', [
        'item'   => $item->only('id', 'code', 'name'),
        'trails' => new StockTrailCollection($item->stockTrails()->orderByDesc('id')->paginate()),
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan histori pergerakan stok item (mutasi / trail)

#### 📄 Vue Component:

`resources/js/Pages/Item/Trail.vue`