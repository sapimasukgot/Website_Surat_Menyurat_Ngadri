@echo off
REM Jalankan berkas ini SEKALI saja di komputer master:
REM klik kanan -> Run as administrator
REM Gunanya: mengizinkan komputer lain mengakses aplikasi (port 8000).

netsh advfirewall firewall delete rule name="Surat Desa Ngadri (port 8000)" >nul 2>&1
netsh advfirewall firewall add rule name="Surat Desa Ngadri (port 8000)" dir=in action=allow protocol=TCP localport=8000

if errorlevel 1 (
    echo [GAGAL] Jalankan berkas ini sebagai Administrator:
    echo klik kanan lalu pilih "Run as administrator".
) else (
    echo Berhasil. Komputer lain kini bisa membuka aplikasi
    echo lewat http://IP-KOMPUTER-INI:8000
)
pause
