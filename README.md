Website sederhana yang mencurahkan isi hati kita di hari itu.
By FarkhansyahOffi

Tutorial Instalasi--

🛠️ 1. INSTALASI DI XAMPP
✳️ Langkah 1: Letakkan Project di htdocs
Ekstrak folder SyukurHarianApp ke:

C:\xampp\htdocs\SyukurHarianApp

✳️ Langkah 2: Jalankan Apache dan MySQL
Buka XAMPP Control Panel

Start Apache dan MySQL

✳️ Langkah 3: Import Database
Buka browser dan akses:

http://localhost/phpmyadmin

Buat database baru:
gratitude_app

Klik database tersebut → pilih Import → upload file .sql dari folder database/ SyukurHarianApp.

✳️ Langkah 4: Akses di Browser

Coba buka:
http://localhost/SyukurHarianApp

🛠️ 2. INSTALASI DI LARAGON
Laragon lebih otomatis dan friendly untuk Laravel/project PHP.
✳️ Langkah 1: Letakkan Project di Laragon
Copy folder SyukurHarianApp ke:
C:\laragon\www\SyukurHarianApp

✳️ Langkah 2: Jalankan Laragon
Buka Laragon

Start semua service (Apache/Nginx & MySQL)
✳️ Langkah 3: Import Database
Buka:
http://localhost/phpmyadmin

Buat database baru: gratitude_app
Import file .sql

✳️ Langkah 4: Akses dengan Domain Lokal
Jika Laragon diatur ke auto-virtualhost, kamu bisa akses:
http://syukurharianapp.test
Jika tidak otomatis, akses:
http://localhost/syukurharianapp

Terimkasihh
-FarkhansyahOffi
