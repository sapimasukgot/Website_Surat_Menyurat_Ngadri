@echo off
chcp 65001 >nul
cd /d "%~dp0"

echo ============================================================
echo   Memperbaiki tautan folder storage (foto profil, dll)
echo ============================================================
echo.

REM Hapus public\storage lama bila ada (folder kosong atau tautan lama)
if exist "public\storage" (
    echo Menghapus public\storage lama...
    rmdir "public\storage" 2>nul
    if exist "public\storage" (
        echo.
        echo [GAGAL] public\storage tidak bisa dihapus otomatis.
        echo Folder itu mungkin berisi file. Hapus manual lalu jalankan lagi.
        echo.
        pause
        exit /b 1
    )
)

echo Membuat tautan public\storage  -^>  storage\app\public ...
mklink /J "public\storage" "%~dp0storage\app\public"

echo.
if exist "public\storage\profil" (
    echo [BERHASIL] Tautan dibuat. Foto profil sekarang akan muncul.
    echo Muat ulang halaman profil di browser ( Ctrl + F5 ).
) else (
    echo [SELESAI] Tautan dibuat. Silakan muat ulang halaman profil.
)
echo.
pause
