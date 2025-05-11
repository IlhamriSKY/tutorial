## 📁 Lokasi File

`routes/web.php`

- - -

## 🔹 Struktur Rute dan Penjelasan

### 🔐 Middleware: `auth:sanctum`

Semua rute dalam grup ini hanya dapat diakses oleh user yang telah login dan diautentikasi. ---

### 📊 Dashboard & Aktivitas

*   `/` → Dashboard utama (`DashboardController@index`)
*   `/activity` → Log aktivitas (`DashboardController@activity`)

\---

### 🌐 Bahasa & Alert Gudang

*   `/language/{language}` → Ganti bahasa (AjaxController)
*   `/alerts` → Lihat alert semua gudang
*   `/alerts/{warehouse}` → Lihat alert per gudang

\---

### ⚙️ Pengaturan Aplikasi

*   `/settings` (GET) → Form pengaturan
*   `/settings` (POST) → Simpan pengaturan

\---

### 🔍 Pencarian & Media

*   `search/items` → Pencarian item
*   `search/contacts` → Pencarian kontak
*   `media/{media}/delete` → Hapus media
*   `media/{media}/download` → Unduh media

\---

### 🧑‍💻 User & Item Tools

*   `users/{user}/disable_2fa` → Nonaktifkan 2FA
*   `items/{item}/photo` → Hapus foto item
*   `items/{item}/trail` → Lihat histori stok item

\---

### 🧱 Resource CRUD (extendedResources)

Semua controller ini menggunakan resource route lengkap (index, create, store, edit, update, destroy):

items, roles, units, storages, users, contacts,
checkins, checkouts, categories, transfers,
warehouses, adjustments

\---

### 📥 Port Controller (Import/Export Excel)

`Route::portResources([...])` memuat semua controller import/export:

items, units, storages, contacts, checkins,
checkouts, categories, warehouses

\---

### 📨 Notifikasi & Preview

*   `notifications/[checkin|checkout|transfer|adjustment]`
*   `preview/[checkin|checkout|transfer|adjustment|low_stock]`

#### 🧾 Fungsinya:

*   Mengirim notifikasi email/WA (langsung)
*   Preview isi email sebelum dikirim

\---

### 📈 Laporan (Reports)

*   `/reports` → Dashboard laporan
*   `/reports/checkin` (GET/POST) → Filter laporan checkin
*   `/reports/checkout` → Laporan barang keluar
*   `/reports/transfer` → Laporan transfer antar gudang
*   `/reports/adjustment` → Laporan penyesuaian stok

#### 🗃️ Ekspor:

*   `/reports/checkin/export`
*   `/reports/checkout/export`

\---

### 🔐 Role & Permission

*   `roles/{role}/permissions` → Atur izin untuk role tertentu

\---

### 🛠️ Perintah Artisan via Route

``prefix: /commands (dengan middleware `throttle + purchased`)``

*   `/commands/storage_link` → Jalankan `artisan storage:link`
*   `/commands/update_database` → Jalankan `artisan migrate --force`

\---

### 🔔 Route Tambahan

*   `/notification` → Halaman info hasil eksekusi command