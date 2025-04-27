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
