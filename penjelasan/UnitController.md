## 📁 Lokasi File

`app/Http/Controllers/UnitController.php`

- - -

## 🔹 CRUD + Tampilan + Search + Query SQL

### 🔧 Function: `store(UnitRequest $request)`

```
public function store(UnitRequest $request)
{
    Unit::create($request->validated());

    return redirect()->route('units.index')
        ->with('message', __choice('action_text', ['record' => 'Unit', 'action' => 'created']));
}
```

#### 📝 Penjelasan:

*   Menyimpan data unit baru ke database
*   Validasi input dilakukan oleh `UnitRequest`

#### 💡 Query SQL setara:

```
INSERT INTO units (name, code, base_unit_id, created_at, updated_at)
VALUES ('Kilogram', 'KG', NULL, NOW(), NOW());
```

- - -

### 🔧 Function: `update(UnitRequest $request, Unit $unit)`

```
public function update(UnitRequest $request, Unit $unit)
{
    $unit->update($request->validated());

    return back()->with('message', __choice('action_text', ['record' => 'Unit', 'action' => 'updated']));
}
```

#### 📝 Penjelasan:

*   Mengupdate data unit berdasarkan ID

#### 💡 Query SQL setara:

```
UPDATE units
SET name = 'Gram', code = 'GR', base_unit_id = 1, updated_at = NOW()
WHERE id = 4;
```

- - -

### 🔧 Function: `destroy(Unit $unit)`

```
public function destroy(Unit $unit)
{
    if ($unit->del()) {
        return redirect()->route('units.index')
            ->with('message', __choice('action_text', ['record' => 'Unit', 'action' => 'deleted']));
    }

    return back()->with('error', __('The record can not be deleted.'));
}
```

#### 📝 Penjelasan:

*   Soft delete unit dengan method custom `del()`

#### 💡 Query SQL setara:

```
UPDATE units
SET deleted_at = NOW()
WHERE id = 4;
```

- - -

### 🔧 Function: `destroyPermanently(Unit $unit)`

```
public function destroyPermanently(Unit $unit)
{
    if ($unit->delP()) {
        return redirect()->route('units.index')
            ->with('message', __choice('action_text', ['record' => 'Unit', 'action' => 'permanently deleted']));
    }

    return back()->with('error', __('The record can not be deleted.'));
}
```

#### 📝 Penjelasan:

*   Menghapus unit secara permanen menggunakan method `delP()`

#### 💡 Query SQL setara:

```
DELETE FROM units WHERE id = 4;
```

- - -

### 🔧 Function: `index(Request $request)`

```
public function index(Request $request)
{
    $filters = $request->all('search', 'trashed');

    return Inertia::render('Unit/Index', [
        'filters' => $filters,
        'units'   => new UnitCollection(
            Unit::filter($filters)
                ->orderByDesc('id')
                ->paginate()
                ->withQueryString()
        ),
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan daftar unit dengan fitur pencarian dan filter data terhapus
*   Menggunakan method custom `filter()` dari model Unit

#### 💡 Contoh SQL jika search = 'gram':

```
SELECT * FROM units
WHERE name LIKE '%gram%'
ORDER BY id DESC
LIMIT 10 OFFSET 0;
```

#### 📄 Vue Component:

`resources/js/Pages/Unit/Index.vue`

- - -

### 🔧 Function: `create()`

```
public function create()
{
    return Inertia::render('Unit/Form', [
        'base_units' => Unit::base()->get(),
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan form untuk menambahkan unit baru
*   Mengirim data unit dasar (`base_units`) ke frontend

#### 💡 Query SQL setara (unit dasar):

```
SELECT * FROM units WHERE base_unit_id IS NULL;
```

#### 📄 Vue Component:

`resources/js/Pages/Unit/Form.vue`

- - -

### 🔧 Function: `edit(Unit $unit)`

```
public function edit
```