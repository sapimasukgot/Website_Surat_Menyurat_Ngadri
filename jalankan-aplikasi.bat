@echo off
title Aplikasi Surat Desa Ngadri
cd /d "%~dp0"

echo ================================================
echo        APLIKASI SURAT DESA NGADRI
echo ================================================
echo.
echo  Pastikan MySQL sudah berjalan (buka Laragon /
echo  XAMPP dan klik Start terlebih dahulu).
echo.

where php >nul 2>&1
if errorlevel 1 (
    echo [GAGAL] PHP tidak ditemukan.
    echo Buka aplikasi lewat Terminal Laragon, atau tambahkan
    echo PHP ke PATH Windows terlebih dahulu.
    pause
    exit /b 1
)

echo Menyiapkan database...
php artisan migrate --force
if errorlevel 1 (
    echo.
    echo [GAGAL] Tidak bisa terhubung ke database.
    echo Pastikan MySQL pada Laragon/XAMPP sudah berjalan, lalu coba lagi.
    pause
    exit /b 1
)

echo.
echo ------------------------------------------------
echo  Akses dari KOMPUTER INI  : http://localhost:8000
echo  Akses dari KOMPUTER LAIN : http://IP-KOMPUTER-INI:8000
echo  IP komputer ini (IPv4):
ipconfig | findstr /i "IPv4"
echo ------------------------------------------------
echo.
echo  JANGAN TUTUP JENDELA INI selama aplikasi dipakai.
echo  Tutup jendela / tekan CTRL+C untuk mematikan aplikasi.
echo.

start "" http://localhost:8000
php artisan serve --host=0.0.0.0 --port=8000
pause
