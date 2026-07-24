@echo off
title Setup Awal - Aplikasi Surat Desa Ngadri
cd /d "%~dp0"

echo.
echo ================================================
echo     SETUP AWAL - APLIKASI SURAT DESA NGADRI
echo ================================================
echo.
echo  File ini hanya perlu dijalankan SEKALI saat
echo  pertama kali menggunakan aplikasi.
echo.
echo  SEBELUM MELANJUTKAN, pastikan:
echo   1. XAMPP sudah terinstall di C:\xampp
echo   2. XAMPP Control Panel sudah dibuka
echo   3. Apache dan MySQL sudah di-START (hijau)
echo.
echo  Tekan tombol apa saja jika sudah siap...
pause >nul

REM ================================================
REM  LANGKAH 1: Cek PHP
REM ================================================
echo.
echo [1/7] Memeriksa PHP...

set "PHP_PATH="
where php >nul 2>&1
if not errorlevel 1 (
    for /f "delims=" %%i in ('where php') do (
        if not defined PHP_PATH set "PHP_PATH=%%i"
    )
) 
if not defined PHP_PATH (
    if exist "C:\xampp\php\php.exe" (
        set "PHP_PATH=C:\xampp\php\php.exe"
    )
)

if not defined PHP_PATH (
    echo        [GAGAL] PHP tidak ditemukan!
    echo.
    echo        Pastikan XAMPP sudah terinstall di C:\xampp
    echo        atau PHP sudah ditambahkan ke PATH Windows.
    echo.
    pause
    exit /b 1
)
echo        PHP ditemukan.                       [OK]

REM ================================================
REM  LANGKAH 2: Cek MySQL
REM ================================================
echo.
echo [2/7] Memeriksa MySQL...

set "MYSQL_PATH="
where mysql >nul 2>&1
if not errorlevel 1 (
    for /f "delims=" %%i in ('where mysql') do (
        if not defined MYSQL_PATH set "MYSQL_PATH=%%i"
    )
)
if not defined MYSQL_PATH (
    if exist "C:\xampp\mysql\bin\mysql.exe" (
        set "MYSQL_PATH=C:\xampp\mysql\bin\mysql.exe"
    )
)

if not defined MYSQL_PATH (
    echo        [GAGAL] MySQL tidak ditemukan!
    echo.
    echo        Pastikan XAMPP sudah terinstall di C:\xampp
    echo.
    pause
    exit /b 1
)
echo        MySQL ditemukan.                     [OK]

REM ================================================
REM  LANGKAH 3: Tanya password MySQL
REM ================================================
echo.
echo [3/7] Mengatur koneksi database...
echo.
echo        Masukkan password MySQL (root).
echo        Jika tidak punya password, langsung tekan ENTER.
echo.
set "DB_PASS="
set /p "DB_PASS=       Password MySQL: "

REM ================================================
REM  LANGKAH 4: Cek koneksi MySQL
REM ================================================
echo.
echo [4/7] Memeriksa koneksi ke MySQL...

if "%DB_PASS%"=="" (
    "%MYSQL_PATH%" -u root -e "SELECT 1;" >nul 2>nul
) else (
    "%MYSQL_PATH%" -u root -p%DB_PASS% -e "SELECT 1;" >nul 2>nul
)
if errorlevel 1 (
    echo        [GAGAL] Tidak bisa terhubung ke MySQL!
    echo.
    echo        Kemungkinan penyebab:
    echo        - Password MySQL yang dimasukkan salah
    echo        - MySQL pada XAMPP belum di-START
    echo.
    echo        Coba jalankan ulang file ini dan masukkan
    echo        password yang benar.
    echo.
    pause
    exit /b 1
)
echo        Koneksi ke MySQL berhasil.           [OK]

REM ================================================
REM  LANGKAH 5: Buat Database
REM ================================================
echo.
echo [5/7] Membuat database...

if "%DB_PASS%"=="" (
    "%MYSQL_PATH%" -u root -e "CREATE DATABASE IF NOT EXISTS administrasi_surat_desa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
) else (
    "%MYSQL_PATH%" -u root -p%DB_PASS% -e "CREATE DATABASE IF NOT EXISTS administrasi_surat_desa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
)
if errorlevel 1 (
    echo        [GAGAL] Tidak bisa membuat database!
    echo.
    pause
    exit /b 1
)
echo        Database "administrasi_surat_desa"   [OK]

REM ================================================
REM  LANGKAH 6: Buat file .env
REM ================================================
echo.
echo [6/7] Mengatur file konfigurasi...

if not exist ".env.example" (
    echo        [GAGAL] File .env.example tidak ditemukan!
    echo        Pastikan Anda menjalankan file ini dari folder aplikasi.
    echo.
    pause
    exit /b 1
)

if exist ".env" (
    echo        File .env sudah ada, membuat backup...
    copy /y ".env" ".env.backup" >nul 2>&1
    echo        Backup disimpan sebagai .env.backup
)

copy /y ".env.example" ".env" >nul 2>&1
if errorlevel 1 (
    echo        [GAGAL] Tidak bisa membuat file .env!
    echo.
    pause
    exit /b 1
)

REM Tulis password MySQL ke file .env
if not "%DB_PASS%"=="" (
    powershell -Command "(Get-Content '.env') -replace '^DB_PASSWORD=.*$', 'DB_PASSWORD=%DB_PASS%' | Set-Content '.env'" >nul 2>&1
)
echo        File .env berhasil dibuat.           [OK]

REM ================================================
REM  LANGKAH 7: Generate APP_KEY + Migrasi + Seed
REM ================================================
echo.
echo [7/8] Menyiapkan aplikasi...
echo        (proses ini memerlukan beberapa detik)
echo.

echo        - Generate kunci aplikasi...
"%PHP_PATH%" artisan key:generate --force --no-interaction >nul 2>&1
if errorlevel 1 (
    echo        [GAGAL] Tidak bisa generate kunci aplikasi!
    echo.
    pause
    exit /b 1
)
echo          Kunci aplikasi berhasil dibuat.     [OK]

echo.
echo        - Membuat tabel database...
"%PHP_PATH%" artisan migrate --force --no-interaction
if errorlevel 1 (
    echo.
    echo        [GAGAL] Tidak bisa membuat tabel database!
    echo        Pastikan MySQL sudah berjalan dan coba lagi.
    echo.
    pause
    exit /b 1
)
echo          Tabel database berhasil dibuat.     [OK]

echo.
echo        - Mengisi data awal...
"%PHP_PATH%" artisan db:seed --force --no-interaction
if errorlevel 1 (
    echo.
    echo        [GAGAL] Tidak bisa mengisi data awal!
    echo.
    pause
    exit /b 1
)
echo          Data awal berhasil diisi.           [OK]

REM ================================================
REM  LANGKAH 8: Storage Link
REM ================================================
echo.
echo [8/8] Membuat link storage...

"%PHP_PATH%" artisan storage:link --no-interaction >nul 2>&1
echo        Link storage berhasil dibuat.        [OK]

REM ================================================
REM  CEK VENDOR FOLDER
REM ================================================
if not exist "vendor\autoload.php" (
    echo.
    echo ================================================
    echo  [PERINGATAN] Folder "vendor" tidak lengkap!
    echo ================================================
    echo.
    echo  Aplikasi membutuhkan folder vendor yang berisi
    echo  library PHP. Pastikan folder "vendor" sudah
    echo  ter-copy lengkap bersama aplikasi ini.
    echo.
    echo  Jika Anda punya Composer, jalankan:
    echo    composer install
    echo.
)

REM ================================================
REM  SELESAI
REM ================================================
echo.
echo ================================================
echo.
echo    SETUP BERHASIL!
echo.
echo ================================================
echo.
echo  PENTING: Ganti password setelah login pertama!
echo.
echo  Selanjutnya, jalankan file:
echo    "jalankan-aplikasi.bat"
echo  untuk membuka aplikasi.
echo.
echo ================================================
echo.
pause
