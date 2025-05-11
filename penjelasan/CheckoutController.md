## 📁 Lokasi File

`app/Http/Controllers/CheckoutController.php`

- - -

## 🔹 CRUD + Notifikasi + Event + Attachment

### 🔧 Function: `index(Request $request)`

*   Menampilkan daftar checkout dengan filter: draft, search, trashed

#### 📄 Vue Component:

`resources/js/Pages/Checkout/Index.vue`

- - -

### 🔧 Function: `create()`

*   Menampilkan form checkout barang keluar
*   Memuat data `contacts` dan `warehouses`

#### 📄 Vue Component:

`resources/js/Pages/Checkout/Form.vue`

- - -

### 🔧 Function: `store(CheckoutRequest $request)`

```
$checkout = (new PrepareOrder(...))->process()->save();
event(new CheckoutEvent($checkout, 'created'));
dispatch(new SendOrderNotifications($checkout, 'checkout'));
```

*   Menyimpan data checkout
*   Trigger event `CheckoutEvent` untuk audit log
*   Kirim notifikasi email & WA jika bukan draft

#### 📩 Email/WA:

*   Email dikirim melalui job `SendOrderNotifications`
*   WA via `NotificationHelper::sendWaNotification()` (dipanggil dari job)

- - -

### 🔧 Function: `edit(Checkout $checkout)`

*   Menampilkan form edit checkout, menggunakan `authorize`
*   Memuat item, variasi, lampiran

#### 📄 Vue Component:

`resources/js/Pages/Checkout/Form.vue`

- - -

### 🔧 Function: `update(CheckoutRequest $request, Checkout $checkout)`

*   Update checkout menggunakan `PrepareOrder`
*   Duplikasi data lama (original) untuk audit
*   Trigger event `CheckoutEvent` status `updated`

- - -

### 🔧 Function: `destroy(Checkout $checkout)`

*   Soft delete checkout dan trigger event `deleted`

#### 💡 SQL Setara:

```
UPDATE checkouts SET deleted_at = NOW() WHERE id = 2;
```

- - -

### 🔧 Function: `destroyPermanently(Checkout $checkout)`

*   Hapus permanen data checkout
*   Event tidak dipanggil (kode dikomentari)

#### 💡 SQL Setara:

```
DELETE FROM checkouts WHERE id = 2;
```

- - -

### 🔧 Function: `restore(Checkout $checkout)`

*   Restore checkout beserta item-itemnya
*   Trigger event `CheckoutEvent` status `restored`

#### 💡 SQL Setara:

```

UPDATE checkouts SET deleted_at = NULL WHERE id = 2;
UPDATE checkout_items SET deleted_at = NULL WHERE checkout_id = 2;
```

- - -

### 🔧 Function: `show(Request $request, Checkout $checkout)`

*   Menampilkan detail checkout dalam bentuk JSON atau tampilan

#### 📄 Vue Component:

`resources/js/Pages/Checkout/Show.vue`