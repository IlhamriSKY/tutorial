## 📁 Lokasi File

`app/Http/Controllers/DashboardController.php`

- - -

## 🔹 Fungsi Statistik + Aktivitas + Chart

### 🔧 Function: `index(Request $request)`

```
public function index(Request $request)
{
    $this->form($request);

    $data = Item::selectRaw('COUNT(*) as items')
        ->addSelect([
            'checkins' => Checkin::selectRaw('COUNT(*)')->active()
                ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]),
            'checkouts' => Checkout::selectRaw('COUNT(*)')->active()
                ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]),
            'previous_checkins' => Checkin::selectRaw('COUNT(*)')->active()
                ->whereBetween('date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]),
            'previous_checkouts' => Checkout::selectRaw('COUNT(*)')->active()
                ->whereBetween('date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]),
            'contacts' => Contact::selectRaw('COUNT(*)'),
        ])->first();

    $chart = new ChartData($request->get('month'), $request->get('year'));

    return Inertia::render('Dashboard/Index', [
        'data'         => $data,
        'top_products' => $chart->topProducts(),
        'chart'        => ['year' => $chart->year(), 'month' => $chart->month()],
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan statistik global dashboard: total item, checkin/checkout bulan ini & sebelumnya
*   Data digabung dalam satu query menggunakan subselect (efisien)
*   `ChartData` menghasilkan data grafik dan produk teratas

#### 💡 Contoh Query SQL (disederhanakan):

```
SELECT
  COUNT(*) as items,
  (SELECT COUNT(*) FROM checkins WHERE date BETWEEN '2025-05-01' AND '2025-05-31') as checkins,
  (SELECT COUNT(*) FROM checkouts WHERE date BETWEEN '2025-05-01' AND '2025-05-31') as checkouts,
  (SELECT COUNT(*) FROM checkins WHERE date BETWEEN '2025-04-01' AND '2025-04-30') as previous_checkins,
  (SELECT COUNT(*) FROM checkouts WHERE date BETWEEN '2025-04-01' AND '2025-04-30') as previous_checkouts,
  (SELECT COUNT(*) FROM contacts) as contacts
FROM items
LIMIT 1;
```

#### 📄 Vue Component:

`resources/js/Pages/Dashboard/Index.vue`

- - -

### 🔧 Function: `activity(Request $request)`

```
public function activity(Request $request)
{
    $authUser = Auth::user()->load('roles');

    $hasRoleTwo = $authUser->roles->contains(fn ($role) => $role->id == 2);

    $query = Activity::query();

    if (! $hasRoleTwo) {
        $query->where('causer_id', $authUser->id);
    }

    $activities = $query->filter($request->only('search'))
                        ->orderByDesc('id')
                        ->paginate();

    return Inertia::render('Activity/Index', [
        'filters'    => $request->all('search'),
        'activities' => new ActivityCollection($activities),
    ]);
}
```

#### 📝 Penjelasan:

*   Menampilkan log aktivitas sistem
*   User dengan role ID 2 (mungkin admin/operator) dapat melihat semua aktivitas
*   User biasa hanya bisa melihat aktivitas milik sendiri

#### 📄 Vue Component:

`resources/js/Pages/Activity/Index.vue`

- - -

### 🔧 Function: `form(Request $request)`

```
public function form(Request $request)
{
    return $request->validate([
        'month' => 'nullable|integer|date_format:n',
        'year'  => 'nullable|integer|date_format:Y',
    ]);
}
```

#### 📝 Penjelasan:

*   Validasi input form untuk filter chart berdasarkan bulan dan tahun
*   Dipanggil internal dari fungsi `index()`