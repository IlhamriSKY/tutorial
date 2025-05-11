## 📁 Lokasi File

`app/Http/Controllers/CheckinController.php`

- - -

## 🔹 Fungsi CRUD + Notifikasi + Event + Validasi

### 🔧 Function: `index(Request $request)`

*   Menampilkan daftar checkin (barang masuk) dengan filter: draft, search, dan data terhapus

#### 📄 Vue Component:

`resources/js/Pages/Checkin/Index.vue`

- - -

### 🔧 Function: `create()`

*   Menampilkan form create checkin baru
*   Memuat daftar kontak dan warehouse

#### 📄 Vue Component:

`resources/js/Pages/Checkin/Form.vue`

- - -

### 🔧 Function: `store(CheckinRequest $request)`

```php
$checkin = (new PrepareOrder(...))->process()->save();
event(new \App\Events\CheckinEvent($checkin, 'created'));
```

*   Menyimpan checkin baru menggunakan service `PrepareOrder`
*   Memicu event `CheckinEvent`
*   Mengirim notifikasi email & WhatsApp via job `SendOrderNotifications` jika bukan draft

#### 📩 Email/WA:

*   Email dikirim menggunakan `EmailCheckin`
*   WA menggunakan `NotificationHelper::sendWaNotification()`

- - -

### 🔧 Function: `edit(Checkin $checkin)`

*   Menampilkan form edit checkin dengan authorization
*   Memuat detail item, variasi, dan lampiran

#### 📄 Vue Component:

`resources/js/Pages/Checkin/Form.vue`

- - -

### 🔧 Function: `update(CheckinRequest $request, Checkin $checkin)`

*   Update checkin menggunakan `PrepareOrder`
*   Data sebelum update disalin menggunakan `replicate()`
*   Memicu event `CheckinEvent` dengan status `updated`

- - -

### 🔧 Function: `destroy(Checkin $checkin)`

*   Soft delete checkin dan trigger event `CheckinEvent` dengan status `deleted`

#### 💡 Query SQL:

```sql
UPDATE checkins SET deleted_at = NOW() WHERE id = 1;
```

- - -

### 🔧 Function: `destroyPermanently(Checkin $checkin)`

*   Hapus permanen checkin
*   `CheckinEvent` tidak dipanggil (kode dikomentari)

#### 💡 Query SQL:

```sql
DELETE FROM checkins WHERE id = 1;
```

- - -

### 🔧 Function: `restore(Checkin $checkin)`

*   Mengembalikan checkin & item-itemnya (restore rekursif)
*   Memicu event `CheckinEvent` dengan status `restored`

#### 💡 Query SQL:

```sql
UPDATE checkins SET deleted_at = NULL WHERE id = 1;
UPDATE checkin_items SET deleted_at = NULL WHERE checkin_id = 1;
```

- - -

### 🔧 Function: `show(Request $request, Checkin $checkin)`

*   Menampilkan detail checkin secara full, baik sebagai JSON atau tampilan halaman

#### 📄 Vue Component:

`resources/js/Pages/Checkin/Show.vue`