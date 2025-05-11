## 📁 Lokasi File

`app/Http/Controllers/UserController.php`

- - -

## 🔹 CRUD + Tampilan + Role-Based Logic + Query SQL

### 🔧 Function: `index(Request $request)`

```
public function index(Request $request)
{
    $filters = $request->only('search', 'role', 'trashed');

    $authUser = Auth::user()->load('roles');

    $hasRoleThree = $authUser->roles->contains(function ($role) {
        return $role->id == 3;
    });

    $query = User::ofAccount()->orderBy('name')->filter($filters);

    if ($hasRoleThree) {
        $query->where('warehouse_id', $authUser->warehouse_id);
    }

    return Inertia::render('User/Index', [
        'filters' => $filters,
        'roles'   => Role::ofAccount()->get(),
        'users'   => new UserCollection($query->paginate()),
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan daftar user dengan filter berdasarkan search, role, dan trash
*   Role ID = 3 hanya boleh melihat user di warehouse yang sama

#### 💡 Contoh Query SQL (jika Role ID 3 dan filter 'admin'):

```
SELECT * FROM users
WHERE warehouse_id = 2 AND name LIKE '%admin%'
ORDER BY name ASC
LIMIT 10 OFFSET 0;
```

#### 📄 Vue Component:

`resources/js/Pages/User/Index.vue`

- - -

### 🔧 Function: `create()`

```
public function create()
{
    $user = Auth::user()->load('roles');

    $roles = Role::ofAccount()->get();

    $hasRoleThree = $user->roles->contains(function ($role) {
        return $role->id == 3;
    });

    $warehouses = $hasRoleThree
        ? Warehouse::where('id', $user->warehouse_id)->get()
        : Warehouse::ofAccount()->active()->get();

    return Inertia::render('User/Form', [
        'roles'      => $roles,
        'warehouses' => $warehouses,
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan form create user dengan akses terbatas berdasarkan role

#### 📄 Vue Component:

`resources/js/Pages/User/Form.vue`

- - -

### 🔧 Function: `store(UserRequest $request)`

```
public function store(UserRequest $request)
{
    $user = User::create($request->validated());
    $user->assignRole($request->input('roles'));

    return redirect()->route('users.index')
        ->with('message', __choice('action_text', ['record' => 'User', 'action' => 'created']));
}
```

#### 📝 Penjelasan:

*   Membuat user baru dan menetapkan peran (role)

#### 💡 Query SQL setara:

```
INSERT INTO users (name, email, password, warehouse_id, created_at, updated_at)
VALUES ('Admin', 'admin@example.com', 'hashedpass', 1, NOW(), NOW());

INSERT INTO model_has_roles (role_id, model_id, model_type)
VALUES (2, 5, 'App\\Models\\User');
```

- - -

### 🔧 Function: `edit(User $user)`

```
public function edit(User $user)
{
    $user->load('roles');

    $roles = Role::ofAccount()->get();

    $hasRoleThree = $user->roles->contains(function ($role) {
        return $role->id == 3;
    });

    $warehouses = $hasRoleThree
        ? Warehouse::where('id', $user->warehouse_id)->get()
        : Warehouse::ofAccount()->active()->get();

    return Inertia::render('User/Form', [
        'edit'       => new UserResource($user),
        'roles'      => $roles,
        'warehouses' => $warehouses,
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan form edit user, data tergantung role user yang sedang login

#### 📄 Vue Component:

`resources/js/Pages/User/Form.vue`

- - -

### 🔧 Function: `update(UserRequest $request, User $user)`

```
public function update(UserRequest $request, User $user)
{
    if ($user->id == auth()->id()) {
        return back()->with('error', __('You should not update your own account.'));
    }

    if (demo() && $user->id == 1) {
        return back()->with('error', 'This feature is disabled on demo');
    }

    $user->update($request->validated());
    $user->syncRoles($request->input('roles'));

    return back()->with('message', __choice('action_text', ['record' => 'User', 'action' => 'updated']));
}
```

#### 📝 Penjelasan:

*   Mengupdate data user dengan validasi khusus untuk user sendiri dan demo user
*   Menyesuaikan role user menggunakan `syncRoles()`

#### 💡 Query SQL setara:

```
UPDATE users
SET name = 'New Name', email = 'new@example.com', updated_at = NOW()
WHERE id = 5;

DELETE FROM model_has_roles WHERE model_id = 5;
INSERT INTO model_has_roles (role_id, model_id, model_type) VALUES (...);
```

- - -

### 🔧 Function: `destroy(User $user)`

```
public function destroy(User $user)
{
    if ($user->id == auth()->id()) {
        return back()->with('error', __('You should not delete your own account.'));
    }

    if ($user->del()) {
        return redirect()->route('users.index')
            ->with('message', __choice('action_text', ['record' => 'User', 'action' => 'deleted']));
    }

    return back()->with('error', __('The record can not be deleted.'));
}
```

#### 📝 Penjelasan:

*   Mencegah user menghapus dirinya sendiri
*   Menghapus user secara soft delete dengan `del()`

#### 💡 Query SQL setara:

```
UPDATE users SET deleted_at = NOW() WHERE id = 5;
```

- - -

### 🔧 Function: `destroyPermanently(User $user)`

```
public function destroyPermanently(User $user)
{
    if ($user->id == auth()->id()) {
        return back()->with('error', __('You should not delete your own account.'));
    }

    if ($user->delP()) {
        return redirect()->route('users.index')
            ->with('message', __choice('action_text', ['record' => 'User', 'action' => 'deleted']));
    }

    return back()->with('error', __('The record can not be deleted.'));
}
```

#### 📝 Penjelasan:

*   Hapus permanen user, dengan perlindungan terhadap akun sendiri

#### 💡 Query SQL setara:

```
DELETE FROM users WHERE id = 5;
```

- - -

### 🔧 Function: `restore(User $user)`

```
public function restore(User $user)
{
    $user->restore();

    return back()->with('message', __choice('action_text', ['record' => 'User', 'action' => 'restored']));
}
```

#### 📝 Penjelasan:

*   Mengembalikan user yang sebelumnya di-soft delete

#### 💡 Query SQL setara:

```
UPDATE users SET deleted_at = NULL WHERE id = 5;
```

- - -

### 🔧 Function: `disable2FA(User $user)`

```
public function disable2FA(User $user)
{
    $user->forceFill(['two_factor_secret' => null, 'two_factor_recovery_codes' => null])->save();

    return back()->with('message', __choice('action_text', ['record' => 'Tow factor authentication', 'action' => 'disabled']));
}
```

#### 📝 Penjelasan:

*   Menonaktifkan fitur Two-Factor Authentication untuk user tertentu

#### 💡 Query SQL setara:

```
UPDATE users
SET two_factor_secret = NULL, two_factor_recovery_codes = NULL, updated_at = NOW()
WHERE id = 5;
```