# Sistem Administrasi Pelayanan Surat Menyurat Desa

Aplikasi web internal untuk perangkat desa dalam membuat berbagai jenis surat secara cepat, konsisten, dan terarsip. Dibangun dengan **Laravel 12 + PHP 8.2**, mengikuti pola **MVC**, *clean code*, *service class*, *Form Request validation*, dan praktik keamanan Laravel.

> Aplikasi ini digunakan **oleh admin/perangkat desa**, bukan oleh warga. Seluruh proses dilakukan dari sisi admin.

---

## Teknologi

| Komponen | Teknologi |
|---|---|
| Framework | Laravel 12 |
| Bahasa | PHP 8.2 |
| Database | MySQL 8 |
| UI | Bootstrap + AdminLTE 3 |
| Autentikasi | Laravel Breeze (pola sesi) |
| Import/Export | Laravel Excel (maatwebsite/excel) |
| Generate Word | PHPWord (phpoffice/phpword) |
| Komponen dropdown | Tom Select |
| Grafik | Chart.js |

> **Catatan versi UI:** AdminLTE 3 dibangun di atas **Bootstrap 4**. Berkas layout memuat AdminLTE + Bootstrap yang konsisten melalui CDN, sehingga aplikasi langsung berjalan tanpa proses build npm. Bila Anda ingin memakai Bootstrap 5 penuh, ganti ke AdminLTE 4.

---

## Fitur Utama

- **Autentikasi** admin (login, throttling percobaan login, proteksi CSRF).
- **Dashboard**: jumlah penduduk, total surat, surat hari ini, grafik surat per bulan, jenis surat terbanyak, aktivitas terbaru.
- **Data Penduduk**: CRUD, pencarian, filter, sorting, pagination.
- **Import Excel** penduduk dengan strategi *upsert* berdasarkan NIK (baru = insert, sudah ada = update, tanpa duplikat) beserta ringkasan & detail error.
- **Export Excel** seluruh data penduduk.
- **Jenis Surat**: CRUD, upload template `.docx`, dan **field tambahan dinamis** per jenis surat.
- **Pembuatan Surat**: pilih jenis + penduduk (dropdown dapat dicari), data pribadi terisi otomatis, isi field tambahan.
- **Penyuntingan & Snapshot**: seluruh isi disimpan sebagai *snapshot* (`data_surat`) sehingga surat lama tetap konsisten meski template atau data penduduk berubah.
- **Generate `.docx`**: mengisi template Word memakai PHPWord; berkas siap dibuka, disunting, dan dicetak.
- **Riwayat Surat**: pencarian, filter, sorting, pagination, sunting ulang, cetak/unduh ulang, hapus.

---

## Kebutuhan Sistem

- PHP >= 8.2 (ekstensi: `pdo_mysql`, `zip`, `gd`, `mbstring`, `xml`, `fileinfo`)
- Composer 2
- MySQL 8 / MariaDB 10.4+
- (Opsional) Node.js 18+ bila ingin membangun aset via Vite

> Tidak perlu LibreOffice atau dependency tambahan apa pun untuk mencetak. Fitur **Print** me-render `.docx` langsung di browser lalu membuka dialog cetak.

### Unduh & Cetak Surat

Setelah surat dibuat, tersedia dua aksi:

- **Print (cetak langsung)** — membuka halaman yang me-render berkas `.docx` di dalam browser (memakai `docx-preview`) lalu **otomatis menampilkan dialog cetak (Ctrl+P)**. Tidak perlu mengunduh atau membuka Microsoft Word, dan tidak butuh software tambahan di komputer/server.
- **Unduh .docx** — mengunduh berkas Word untuk diedit di Microsoft Word bila diperlukan.

Halaman Print memuat pustaka `docx-preview` & `jszip` via CDN, sehingga komputer klien perlu akses internet saat pertama kali mencetak (untuk memuat pustaka tersebut).

---

## Instalasi

Proyek ini berisi seluruh kode aplikasi (folder `app/`, `database/`, `routes/`, `resources/`, `config/`, dll). Framework Laravel diambil melalui Composer.

```bash
# 1. Masuk ke folder proyek
cd Surat_menyurat_Ngadri

# 2. Salin env dan sesuaikan koneksi database
cp .env.example .env

# 3. Pasang dependency PHP (mengunduh Laravel, Excel, PHPWord, dsb.)
composer install

# 4. Generate application key
php artisan key:generate

# 5. Buat database bernama "administrasi_surat_desa" di MySQL,
#    lalu jalankan migrasi + seeder
php artisan migrate --seed

# 6. Buat symlink storage agar file template & surat dapat diakses publik
php artisan storage:link

# 7. (Opsional) build aset front-end
# npm install && npm run build

# 8. Jalankan aplikasi
php artisan serve
```

Akses di `http://localhost:8000`.

### Akun default (dari seeder)

| Email | Kata sandi | Jabatan |
|---|---|---|
| `admin@desa.test` | `password` | Sekretaris Desa |

Ganti kata sandi melalui menu **Profil** setelah login pertama.

---

## Struktur Kode

```
app/
├── Http/
│   ├── Controllers/       # DashboardController, PendudukController, JenisSuratController, SuratController, Auth/, ProfileController
│   └── Requests/          # Form Request (validasi terpusat & sanitasi)
├── Imports/               # PendudukImport (Laravel Excel, upsert by NIK)
├── Exports/               # PendudukExport
├── Models/                # User, Penduduk, JenisSurat, Surat, ImportLog
└── Services/              # PendudukImportService, NomorSuratService, SuratGeneratorService
database/
├── migrations/            # skema tabel
├── seeders/               # UserSeeder, JenisSuratSeeder, PendudukSeeder
├── factories/             # data uji
└── templates/             # template .docx contoh (sktm.docx) yang di-seed
resources/views/           # Blade: layouts, partials, dashboard, penduduk, jenis_surat, surat, auth
routes/                    # web.php, auth.php, console.php
config/desa.php            # identitas desa (kop & penomoran)
```

### Relasi Model

- `User` **hasMany** `Surat`, `ImportLog`
- `Penduduk` **hasMany** `Surat`
- `JenisSurat` **hasMany** `Surat`
- `Surat` **belongsTo** `User`, `Penduduk`, `JenisSurat`

---

## Template Surat (.docx) & Placeholder

Template dibuat di Microsoft Word memakai sintaks placeholder `${nama_placeholder}`. Saat generate, PHPWord mengganti placeholder dengan nilai dari *snapshot* surat.

**Placeholder identitas & sistem** (otomatis):
`${nomor_surat}`, `${tanggal}`, `${desa}`, `${kecamatan}`, `${kabupaten}`, `${provinsi}`, `${penandatangan}`, `${jabatan_ttd}`

**Placeholder data penduduk** (otomatis dari database):
`${nama}`, `${nik}`, `${no_kk}`, `${tempat_lahir}`, `${tanggal_lahir}`, `${jenis_kelamin}`, `${agama}`, `${pekerjaan}`, `${pendidikan}`, `${status_kawin}`, `${alamat}`, `${rt}`, `${rw}`, `${dusun}`, `${no_hp}`

**Placeholder field tambahan** (mengikuti jenis surat, contoh):
`${keperluan}`, `${penghasilan}`, `${nama_usaha}`, `${tujuan}`, `${lama_tinggal}`, `${keterangan_tambahan}`, dst. Nama placeholder = versi *slug* dari label field (mis. label "Nama Usaha" → `${nama_usaha}`).

Template contoh SKTM tersedia di `database/templates/sktm.docx` dan otomatis terpasang saat `migrate --seed`.

Identitas desa diatur di berkas `.env` (`DESA_NAMA`, `DESA_KECAMATAN`, `DESA_KABUPATEN`, `DESA_KODE`).

---

## Keamanan

- **Prepared statements** otomatis via Eloquent/Query Builder (proteksi SQL injection).
- **Mass-assignment protection** melalui properti `$fillable` pada setiap model.
- **Validasi & sanitasi input** terpusat di Form Request (termasuk normalisasi NIK/No KK).
- **Validasi upload**: pembatasan ekstensi (`mimes`) **dan** tipe MIME (`mimetypes`) untuk `.xlsx`/`.docx`, plus batas ukuran berkas.
- **Nama berkas acak** saat menyimpan template (mencegah *path traversal* & tabrakan nama).
- **CSRF protection** aktif pada seluruh form.
- **Throttling login** (maksimal 5 percobaan) untuk mencegah *brute force*.
- **Soft delete** pada penduduk, jenis surat, dan surat untuk menjaga integritas riwayat.

---

## Penomoran Surat

Format resmi: `{kode_klasifikasi}/{urut3}/{kode_desa}/{tahun}`
Contoh: `470/001/409.40.13/2026`

Format diatur di `config/nomor_surat.php`. Kode klasifikasi arsip diisi per jenis
surat lewat menu **Jenis Surat**; bila dikosongkan, segmennya otomatis dibuang.

**Nomor urut** adalah **satu urutan berjalan yang dipakai bersama oleh seluruh
surat keluar** — tidak dipisah per jenis surat maupun per kode klasifikasi,
sesuai buku agenda surat kantor desa. Aturannya:

- Nomor berikutnya = nomor urut **tertinggi** yang terpakai pada tahun tanggal
  surat, ditambah satu. Surat yang dihapus tetap diperhitungkan agar nomornya
  tidak dipakai ulang.
- Bila operator menyunting nomor surat secara manual ke angka yang **lebih
  tinggi**, angka itu otomatis menjadi **patokan baru** dan surat berikutnya
  melanjutkan dari sana (berguna saat menyelaraskan dengan buku agenda manual).
- **Ganti tahun → otomatis kembali ke 001**, tanpa reset manual.
- Angka urut disimpan di kolom `surats.nomor_urut`, dan pengambilannya dikunci
  di dalam transaksi (`lockForUpdate`) sehingga dua operator yang menyimpan
  bersamaan tidak mendapat nomor kembar.

---

## Pengujian

```bash
php artisan test
```

Tersedia uji fitur untuk autentikasi, CRUD penduduk (termasuk keunikan NIK), format nomor surat, dan pembuatan draft surat (snapshot).

---

## Deployment (ringkas)

1. Set `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY` sudah di-generate, kredensial DB produksi.
2. `composer install --optimize-autoloader --no-dev`
3. `php artisan migrate --force`
4. `php artisan storage:link`
5. `php artisan config:cache route:cache view:cache`
6. Arahkan document root web server ke folder `public/`.
7. Pastikan folder `storage/` dan `bootstrap/cache/` dapat ditulis oleh web server.

---

## Lisensi

MIT.
