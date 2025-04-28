Tutorial Menjalankan Program Laboratory Supply Tracking Notification System
===========================================================================

1.  *Buka Command Prompt (CMD)*
    -Tekan `Windows + R`, ketik `cmd`, lalu tekan Enter.
2.  *Pindah ke drive D ketikan di CMD*
    
        D:
    
3.  *Masuk ke folder project*
    
        cd D:\xampp\htdocs\Laboratory-Supply-Tracking-Notification-System
    
4.  *Jalankan XAMPP Control Panel*
    -Buka folder `D:\xampp`.
    -Jalankan file `xampp-control.exe`.
5.  *Aktifkan Apache dan MySQL*
    -Klik tombol *Start* pada Apache dan MySQL di XAMPP Control Panel.
6.  *Jalankan server lokal menggunakan Artisan*
    
        php artisan serve
    
7.  *Jika terjadi error saat menjalankan "php artisan serve"*
    -Jalankan perintah:
        
            composer update
        
    -Pastikan antivirus atau Windows Defender dinonaktifkan sementara.
8.  *Buka browser*
    -Ketik alamat berikut:
        
            127.0.0.1:8000
        
9.  *Login ke aplikasi*
    -*Username:* `admin1`
    -*Password:* `gundag2025`


# SUMMERY PERTANYAAN
MVC di Laravel
==============

*MVC* = *Model*, *View*, *Controller*

-   Model = berhubungan dengan database
-   View = tampilan halaman (HTML, Vue, dll)
-   Controller = pengatur logika (request dari browser, proses data, kirim balikan)

* * *

1\. Bagaimana cara koneksi ke database di Laravel?
--------------------------------------------------

-   Laravel pakai file `.env` untuk setting koneksi database.
-   Contoh setting di `.env`:
    
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=nama_database
        DB_USERNAME=root
        DB_PASSWORD=password
        
    
-   Setting ini otomatis dibaca oleh `config/database.php`.
-   Model koneksi ke tabel ada di folder `app/Models/NamaModel.php`.

* * *

2\. Tabel di database banyak sekali, mana yang dipakai?
-------------------------------------------------------

-   Fokus ke tabel-tabel yang ada *modelnya* di folder `app/Models/`.
-   Kalau ada model `Checkin.php`, berarti tabel `checkins` digunakan.
-   *Yang tidak ada modelnya* biasanya bisa dihapus. (Tetap backup dulu sebelum hapus!)
  > Nanti aku hapus dan kirim diagram databasenya

* * *

3\. Bagaimana cara mengedit tampilan (View)?
--------------------------------------------

-   Edit file tampilan di folder `resources/js/pages/`.
-   Contohnya, edit file `index.vue` untuk mengubah halaman index.
-   Setelah mengedit file Vue, jalankan perintah:
    
        npm run build
    
    untuk menyimpan dan memperbaharui hasil build aplikasi.

* * *

4\. Bagaimana cara insert data baru ke database?
------------------------------------------------

-   Cari function `store()` di Controller terkait.

```
    public function store(CheckinRequest $request)
    {
        $data = $request->validated();
    
        $checkin = (new PrepareOrder($data, $request->file('attachments'), new Checkin()))
            ->process()
            ->save();
    
        event(new \App\Events\CheckinEvent($checkin, 'created'));
    
        if (!$checkin->draft && $checkin->contact && $checkin->contact->email) {
            $checkin->load([
                'items.variations',
                'items.item:id,code,name,track_quantity,track_weight',
                'contact',
                'warehouse',
                'items.unit:id,code,name',
                'user:id,name,username,email',
            ]);
    
            dispatch(new SendOrderNotifications($checkin, 'checkin'));
        }
    
        return redirect()->route('checkins.index')->with('message', 'Checkin berhasil dibuat.');
    }
```

### Penjelasan Sederhana:

-   Data form divalidasi dan diambil.
-   Data diproses dan disimpan ke database.
-   Kalau berhasil, bisa kirim email atau notifikasi ke user.
-   Lalu, redirect ke halaman daftar checkins.

### Contoh Raw SQL vs Eloquent:

*Raw SQL*:

    INSERT INTO checkins (nama_kolom, kolom_lain) VALUES ('isi1', 'isi2');

*Laravel Eloquent*:

    $checkin = new Checkin();
    $checkin->nama_kolom = 'isi1';
    $checkin->kolom_lain = 'isi2';
    $checkin->save();
    

* * *

5\. Bagaimana cara hapus data dari database?
--------------------------------------------

-   Cari function `destroy()` di Controller.
```
    public function destroy(Checkin $checkin)
    {
        $checkin->load(['items.item', 'items.unit', 'items.variations']);
        if ($checkin->del()) {
            event(new \App\Events\CheckinEvent($checkin, 'deleted'));
            return redirect()->route('checkins.index')->with('message', 'Checkin berhasil dihapus.');
        }
    
        return redirect()->route('checkins.index')->with('error', 'Data tidak bisa dihapus.');
    }
```
    

### Penjelasan Sederhana:

-   Ambil data Checkin yang mau dihapus.
-   Hapus data tersebut.
-   Kalau sukses, redirect ke daftar dengan pesan sukses.

### Contoh Raw SQL vs Eloquent:

*Raw SQL*:

    DELETE FROM checkins WHERE id = 1;

*Laravel Eloquent*:

    $checkin = Checkin::find(1);
    $checkin->delete();
    

* * *

6\. Bagaimana cara update/edit data di database?
------------------------------------------------

-   Cari function `update()` di Controller.
```
    public function update(CheckinRequest $request, Checkin $checkin)
    {
        $this->authorize('update', $checkin);
        $data = $request->validated();
        $original = $checkin->load(['items.item', 'items.unit', 'items.variations'])->replicate();
        $checkin = (new PrepareOrder($data, $request->file('attachments'), $checkin))
            ->process()
            ->save();
    
        event(new \App\Events\CheckinEvent($checkin, 'updated', $original));
        session()->flash('message', 'Checkin berhasil diupdate.');
    
        return $request->listing == 'yes' ? redirect()->route('checkins.index') : back();
    }
```

### Penjelasan Sederhana:

-   Validasi data yang dikirim user.
-   Backup data lama sebelum update.
-   Update data baru ke database.
-   Kalau sukses, kirim notifikasi bahwa data berhasil diupdate.

### Contoh Raw SQL vs Eloquent:

*Raw SQL*:

    UPDATE checkins SET nama_kolom = 'baru' WHERE id = 1;

*Laravel Eloquent*:

    $checkin = Checkin::find(1);
    $checkin->nama_kolom = 'baru';
    $checkin->save();
    

* * *

Ringkasan Super Singkat
=====================

-   *Model* = Mengelola koneksi dan operasi database.
-   *View* = Mengelola tampilan halaman, contoh file `.vue`.
-   *Controller* = Mengatur alur data antara Model dan View.

* * *

Fungsi CRUD (Create, Read, Update, Delete)
------------------------------------------

Aksi

Function di Controller

Penjelasan

Contoh Raw SQL

Contoh Eloquent

*Insert*

`store()`

Menambahkan data baru ke database.

`INSERT INTO table (col) VALUES ('value');`

`$model = new Model; $model->col = 'value'; $model->save();`

*Update*

`update()`

Memperbarui data yang sudah ada.

`UPDATE table SET col='new' WHERE id=1;`

`$model = Model::find(1); $model->col = 'new'; $model->save();`

*Delete*

`destroy()`

Menghapus data dari database.

`DELETE FROM table WHERE id=1;`

`$model = Model::find(1); $model->delete();`

* * *

Proses Edit Tampilan (View)
---------------------------

-   Edit file `.vue` di `resources/js/pages/`.
-   Setelah mengedit, jalankan perintah:
    
        npm run build
    
    untuk membangun ulang aplikasi dan melihat perubahan.

* * *

Ringkasan Connection Database
-----------------------------

-   Setting koneksi ada di file `.env`.
-   Model tabel ada di `app/Models/`.
-   Fokus hanya pada tabel yang ada model-nya. Tabel lain bisa dihapus (dengan hati-hati).
