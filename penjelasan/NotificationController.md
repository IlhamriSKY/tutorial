## 📁 Lokasi File

`app/Http/Controllers/NotificationController.php`

- - -

## 🔹 Fungsi Notifikasi: Email dan WhatsApp

### 🔧 Function: `adjustment(Request $request, Adjustment $adjustment)`

*   📤 Mengirim email notifikasi penyesuaian stok ke semua user dengan role "Super Admin"
*   👀 Jika route mengandung `preview`, hanya menampilkan template email

#### 📩 Email:

*   Dikirim melalui `EmailAdjustment`

- - -

### 🔧 Function: `checkin(Request $request, Checkin $checkin)`

*   📤 Mengirim email ke kontak yang dituju + BCC ke user saat ini
*   📱 Mengirim notifikasi WhatsApp ke kontak jika memiliki nomor telepon
*   👀 Jika `preview`, menampilkan email tanpa mengirim

#### 🟢 Format WhatsApp:

```

✅ Checkin Barang

Tanggal: 11 Mei 2025
Referensi: CHK-001
Dibuat oleh: Admin

Untuk: PT. ABC
No. Telp: 0812...
Email: contact@abc.com

Barang:
- Item A (ITM001)  Qty: 10 Box

📨 Detail lengkap telah dikirim ke email: contact@abc.com
```

#### 📩 Email:

*   Dikirim melalui `EmailCheckin`

- - -

### 🔧 Function: `checkout(Request $request, Checkout $checkout)`

*   📤 Sama seperti `checkin`, tapi untuk proses pengeluaran barang
*   Email dikirim ke kontak dan WhatsApp jika tersedia

#### 🟢 Format WhatsApp:

```

📦 Checkout Barang

Tanggal: 11 Mei 2025
Referensi: COT-001
Dibuat oleh: Admin

Untuk: PT. ABC
No. Telp: 0812...
Email: contact@abc.com

Barang:
- Item B (ITM002)  Qty: 5 Liter

📨 Detail lengkap telah dikirim ke email: contact@abc.com
```

#### 📩 Email:

*   Dikirim melalui `EmailCheckout`

- - -

### 🔧 Function: `transfer(Request $request, Transfer $transfer)`

*   📤 Mengirim notifikasi email ke warehouse tujuan
*   👀 Jika `preview`, tampilkan tampilan email

#### 📩 Email:

*   Dikirim melalui `EmailTransfer`

- - -

### 🔧 Function: `stock(Request $request)`

*   📉 Mengecek stok rendah dari semua warehouse aktif
*   📤 Mengirim alert email ke semua user dengan role "Super Admin"

#### 📩 Email:

*   Dikirim melalui `LowStockAlert`

- - -

#### 🛡️ Proteksi Fitur Demo

*   Semua fungsi mengecek `demo()` sebelum mengirim email
*   Jika mode demo, ditampilkan link preview email

#### 📦 Komponen Terkait:

*   Mail class: `EmailCheckin`, `EmailCheckout`, `EmailTransfer`, `EmailAdjustment`, `LowStockAlert`
*   Helper: `NotificationHelper::sendWaNotification()`