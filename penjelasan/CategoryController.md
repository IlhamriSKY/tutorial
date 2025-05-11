## 📁 Lokasi File

`app/Http/Controllers/CategoryController.php`

- - -

## 🔹 CRUD + Tampilan + Search + Query SQL

### 🔧 Function: `store(CategoryRequest $request)`

```
public function store(CategoryRequest $request)
{
    Category::create($request->validated());

    return redirect()->route('categories.index')
        ->with('message', __choice('action_text', ['record' => 'Category', 'action' => 'created']));
}
```

#### 📝 Penjelasan:

*   Menyimpan data kategori baru ke database
*   Validasi dilakukan oleh `CategoryRequest`

#### 💡 Query SQL setara:

```
INSERT INTO categories (name, code, parent_id, created_at, updated_at)
VALUES ('Nama Kategori', 'KT001', 1, NOW(), NOW());
```

- - -

### 🔧 Function: `update(CategoryRequest $request, Category $category)`

```
public function update(CategoryRequest $request, Category $category)
{
    $category->update($request->validated());

    return back()->with('message', __choice('action_text', ['record' => 'Category', 'action' => 'updated']));
}
```

#### 📝 Penjelasan:

*   Update data kategori berdasarkan ID

#### 💡 Query SQL setara:

```
UPDATE categories
SET name = 'Kategori Baru', code = 'KT002', parent_id = 2, updated_at = NOW()
WHERE id = 5;
```

- - -

### 🔧 Function: `destroy(Category $category)`

```
public function destroy(Category $category)
{
    if ($category->del()) {
        return redirect()->route('categories.index')
            ->with('message', __choice('action_text', ['record' => 'Category', 'action' => 'deleted']));
    }

    return back()->with('error', __('The record can not be deleted.'));
}
```

#### 📝 Penjelasan:

*   Soft delete data menggunakan method `del()`

#### 💡 Query SQL setara:

```
UPDATE categories
SET deleted_at = NOW()
WHERE id = 5;
```

- - -

### 🔧 Function: `destroyPermanently(Category $category)`

```
public function destroyPermanently(Category $category)
{
    if ($category->delP()) {
        return redirect()->route('categories.index')
            ->with('message', __choice('action_text', ['record' => 'Category', 'action' => 'permanently deleted']));
    }

    return back()->with('error', __('The record can not be deleted.'));
}
```

#### 📝 Penjelasan:

*   Menghapus data kategori secara permanen dengan method `delP()`

#### 💡 Query SQL setara:

```
DELETE FROM categories WHERE id = 5;
```

- - -

### 🔧 Function: `index(Request $request)`

```
public function index(Request $request)
{
    $filters = $request->all('search', 'parent', 'trashed');

    return Inertia::render('Category/Index', [
        'filters'    => $filters,
        'parents'    => Category::onlyParents()->get(),
        'categories' => new CategoryCollection(
            Category::with('parent:id,name,code')
                    ->filter($filters)
                    ->orderByDesc('id')
                    ->paginate()
                    ->withQueryString()
        ),
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan data kategori
*   Mendukung filter pencarian dan trashed
*   Menggunakan `CategoryCollection` dan relasi `parent`

#### 💡 Contoh SQL Query jika search = 'elektronik'

```
SELECT categories.*, parents.name as parent_name
FROM categories
LEFT JOIN categories AS parents ON categories.parent_id = parents.id
WHERE categories.name LIKE '%elektronik%'
ORDER BY categories.id DESC
LIMIT 10 OFFSET 0;
```

#### 📄 Vue Component:

`resources/js/Pages/Category/Index.vue`

- - -

### 🔧 Function: `create()`

```
public function create()
{
    return Inertia::render('Category/Form', [
        'parents' => Category::onlyParents()->get(),
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan form untuk tambah data baru

#### 💡 Query SQL (ambil kategori induk):

```
SELECT * FROM categories WHERE parent_id IS NULL;
```

#### 📄 Vue Component:

`resources/js/Pages/Category/Form.vue`

- - -

### 🔧 Function: `edit(Category $category)`

```
public function edit(Category $category)
{
    return Inertia::render('Category/Form', [
        'parents' => Category::onlyParents()->get(),
        'edit'    => new CategoryResource($category),
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan form edit untuk data kategori tertentu
*   Mengirim data yang akan diedit ke frontend

#### 📄 Vue Component:

`resources/js/Pages/Category/Form.vue`