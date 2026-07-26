# PRD & Implementation Plan — Revisi Pasca-Demo Final
## Aplikasi Surat Menyurat Desa Ngadri

**Disusun:** 26 Juli 2026
**Status project:** Sudah demo final ke perangkat desa. Aplikasi sudah dianggap jadi/lengkap oleh tim — sisa pekerjaan HANYA 5 poin revisi dari hasil demo di bawah ini. Tidak ada penambahan fitur baru di luar 5 poin ini (tidak ada v2, tidak ada gap-filling seperti role/approval/dst yang sempat dibahas sebelumnya — itu semua di-drop dari scope).

---

## Ringkasan 5 Revisi

| # | Revisi | Kompleksitas |
|---|---|---|
| 1 | Opsi pakai kop / tanpa kop, berlaku untuk semua jenis surat (bukan cuma Surat Pernyataan) | Sedang |
| 2 | Format nomor surat baru: `{kode klasifikasi}/{no urut}/{kode desa}/{tahun}`, kode klasifikasi diisi bebas per jenis surat lewat form (bukan daftar tetap) | Kecil–Sedang (banyak dukungannya sudah ada) |
| 3 | Tambah kartu jumlah laki-laki, perempuan, kepala keluarga di dashboard | Kecil |
| 4 | Fitur export Excel "Laporan Adminduk" seperti contoh file yang dikirim kantor desa | Sedang |
| 5 | Ganti template `.docx` untuk beberapa jenis surat + 1 jenis surat baru sesuai file dari kantor desa | Sedang (5 file operasional, 2 lainnya butuh field type `select` baru + `cloneBlock`) |

Detail tiap poin di bawah, sudah dicek langsung ke kode yang ada supaya rencananya konkret dan konsisten dengan pola yang sudah dipakai.

---

## Status Implementasi (26 Juli 2026)

Seluruh **pekerjaan koding untuk 5 poin sudah selesai**. Yang tersisa hanya langkah operasional (upload template) dan pengujian di komputer sendiri.

**Sudah dikerjakan:**

- Poin 1 — kolom `pakai_kop` di tabel `surats`, saklar "Gunakan kop surat" di form buat & sunting surat, penghapusan tabel kop otomatis di `SuratGeneratorService::hapusKop()`.
- Poin 2 — kolom `kode_klasifikasi` di tabel `jenis_surats`, input "Kode Klasifikasi" di form Jenis Surat, placeholder `{kode_klasifikasi}`, format baru `{kode_klasifikasi}/{urut3}/{kode_desa}/{tahun}`, segmen kosong dibuang otomatis. Kode klasifikasi 13 jenis surat sudah diisi lewat seeder.
- Poin 3 — tiga kartu (laki-laki, perempuan, kepala keluarga) di dashboard, masing-masing menautkan ke daftar penduduk yang sudah terfilter.
- Poin 4 — menu **Laporan Adminduk** di halaman Data Penduduk (`app/Exports/AdmindukExport.php` + 2 sheet). Section disabilitas di-drop sesuai keputusan; pekerjaan tetap teks bebas dan dicocokkan ke ~99 kategori resmi, sisanya masuk "PEKERJAAN LAINNYA".
- Poin 5 — tipe field baru **Pilihan (dropdown)**, blok kondisional `${nama_field}` ... `${/nama_field}` di template lewat PHPWord `cloneBlock`, field SIK (termasuk saklar "Ada Hiburan?" + data panitia hiburan) dan jenis surat baru **Surat Keterangan Kepergian Anggota Keluarga** (kode `SKKP`, klasifikasi 470) sudah masuk seeder.

**Yang masih perlu dikerjakan manual (bukan koding):**

1. Jalankan `php artisan migrate` lalu `php artisan db:seed --class=JenisSuratSeeder` di komputer yang dipakai.
2. Isi `DESA_KODE=409.40.13` di berkas `.env` (saat ini masih `DS-NGD`).
3. Upload 5 template yang sudah cocok lewat menu Jenis Surat → Edit → Upload Template.
4. Siapkan template SIK gabungan: pakai berkas "ijin keramaian ada hiburan", bungkus bagian surat pernyataan panitia dengan `${hiburan}` di atas dan `${/hiburan}` di bawah (masing-masing di barisnya sendiri), lalu upload ke jenis surat SIK.
5. Upload template untuk jenis surat baru SKKP, sesuaikan placeholder-nya dengan nama field yang tampil di menu Jenis Surat.
6. Uji coba: buat satu surat per jenis, cek hasil cetaknya, termasuk sekali dengan kop dan sekali tanpa kop.

---

## 1. Opsi Kop Surat / Tanpa Kop

**Permintaan:** Awalnya cuma diminta untuk Surat Pernyataan, tapi lebih baik dibuat opsi umum di form "Buat Surat" untuk semua jenis surat.

**Kondisi kode saat ini:**
- Kop surat (tabel berisi logo kabupaten + logo desa, dikenali lewat teks `"PEMERINTAH"` di dalam tabel pertama) sudah ada logikanya di `app/Services/TemplateLogoService.php` — dipakai untuk menempelkan logo ke template saat logo diganti di menu Pengaturan.
- `SuratGeneratorService::generate()` murni mengisi placeholder template dan tidak tahu-menahu soal kop.

**Rencana implementasi:**
1. Tambah kolom `pakai_kop` (boolean, default `true`) di tabel `surats` lewat migration baru — ini pilihan per-surat (bukan per jenis surat), sesuai keinginan office untuk pilih saat "buat surat".
2. Tambah checkbox "Gunakan kop surat" di `surat/create.blade.php` (dan `edit.blade.php`), default tercentang.
3. Di `SuratGeneratorService::generate()`, setelah `$processor->saveAs(...)`, kalau `pakai_kop === false`: buka ulang file hasil generate pakai `ZipArchive` + `DOMDocument` (pola yang sama persis dengan `TemplateLogoService::findKopCells()`), cari tabel yang mengandung teks `"PEMERINTAH"`, lalu hapus node tabel tersebut (`removeChild`) sebelum file final disimpan.
4. Ini artinya **tidak perlu upload template kedua** per jenis surat — satu template tetap dipakai, kop dihapus terprogram saat generate. Konsisten dengan pola manipulasi XML yang sudah ada di `TemplateLogoService`, jadi tidak menambah teknik baru ke codebase.

**Catatan:** dari file revisi yang dikirim kantor desa, ada `Surat Pernyataan Ada kepala desa.docx` dan `SURAT PERNYATAAN tanpa kepala desa.docx` — dua versi ini nantinya cukup jadi 1 template (yang ada kop), dengan opsi tanpa kop di-generate otomatis lewat mekanisme di atas. Tidak perlu simpan 2 file template.

---

## 2. Format Nomor Surat per Jenis Surat + Kode Klasifikasi

**Keputusan final:** Format `{kode klasifikasi}/{no urut}/{kode desa}/{tahun}`. Kode klasifikasi **tidak dibuatkan daftar tetap di kode** — setiap jenis surat isi sendiri angkanya lewat form Jenis Surat di aplikasi (self-service), termasuk untuk jenis surat yang kemarin tidak dikirimi kodenya (SIK, SKBK, SPN, Surat Pengantar). Jadi tidak ada lagi yang perlu dikonfirmasi ke kantor desa untuk poin ini — operator tinggal isi sendiri kapan saja lewat menu yang sudah ada.

**Kondisi kode saat ini (kabar baik — sebagian besar mekanismenya sudah ada):**
- `config/nomor_surat.php` sudah mendukung format berbeda per `kode_surat` lewat array `formats`, dengan `default_format` sebagai fallback.
- `NomorSuratService::generate()` sudah mendukung placeholder `{urut}/{urut2}/{urut3}/{urut4}`, `{kode_surat}`, `{kode_desa}`, `{bulan}/{bulan2}/{bulan_romawi}`, `{tahun}`.
- **Yang belum ada:** placeholder untuk "kode klasifikasi" numerik (470, 422.5, dst) dan tempat menyimpannya — ini beda dari `{kode_surat}` yang isinya singkatan huruf (SKTM, SKCK, dll).

**Rencana implementasi:**
1. Tambah kolom `kode_klasifikasi` (string, nullable) di tabel `jenis_surats` lewat migration baru.
2. Tambah field input "Kode Klasifikasi" (bebas isi, mis. `470`, `422.5`, `400`) di form Jenis Surat (`jenis_surat/_form.blade.php`) — ini yang jadi "opsi isi sendiri" yang diminta, berlaku untuk semua jenis surat tanpa terkecuali.
3. Tambah placeholder `{kode_klasifikasi}` di `NomorSuratService::generate()`, ambil dari `$jenisSurat->kode_klasifikasi`.
4. Ubah `default_format` di `config/nomor_surat.php` jadi `{kode_klasifikasi}/{urut3}/{kode_desa}/{tahun}` — cukup satu format global karena kode klasifikasi sekarang sudah jadi data per jenis surat, bukan hardcode per kode_surat lagi (array `formats` tetap dipertahankan di config untuk jenis surat yang butuh format benar-benar berbeda di masa depan, tidak dihapus).
5. Tangani kasus kode klasifikasi kosong: kalau `kode_klasifikasi` belum diisi untuk suatu jenis surat, `NomorSuratService` harus menghilangkan segmennya (bukan menyisakan slash kosong di depan, mis. jadi `007/409.40.13/2026` bukan `/007/409.40.13/2026`).
6. Pastikan `config('desa.kode')` (di `config/desa.php`, saat ini default `DS-NGD` dari `.env`) diisi dengan kode wilayah resmi desa (mis. `409.40.13`) — tinggal update `.env`.

**Data awal (boleh langsung diisi lewat form, tidak wajib nunggu development):**

| Jenis Surat (nama di app) | kode_surat | Kode Klasifikasi |
|---|---|---|
| SKTM untuk Beasiswa/Sekolah Anak | SKTM-BEA | 422.5 |
| SKTM untuk Pemasangan Listrik PLN Gratis | SKTM-PLN | 400 |
| Surat Keterangan Ahli Waris | SKAW | 470 |
| Surat Keterangan Belum Menikah | SKBM | 470 |
| Surat Keterangan Domisili | SKD | 470 |
| Surat Keterangan Domisili Usaha | SKDU | 470 |
| Surat Keterangan Kehilangan | SKH | 470 |
| Surat Keterangan Kelahiran | SKL | 474.1 |
| Surat Keterangan Penghasilan | SKP | 422.5 |
| Surat Keterangan Tanah | SKT | 590 |
| Surat Keterangan Tidak Mampu (SKTM) | SKTM | 420 |
| Surat Keterangan Usaha (SKU) | SKU | 470 |
| Surat Keterangan (Umum) | SK | 470 |
| Surat Izin Keramaian | SIK | 470 (terkonfirmasi dari nomor di file asli) |
| SKBK, SPN, Surat Pengantar (SKCK) | SKBK / SPN / SKCK | Belum ada — diisi mandiri oleh operator lewat form kapan saja |
| Ket. Kepergian Anggota Keluarga (jenis surat baru, lihat poin 5) | — (kode baru, mis. SKKB) | 470 (terkonfirmasi dari nomor di file asli) |

---

## 3. Kartu Statistik Dashboard: Laki-laki, Perempuan, Kepala Keluarga

**Kondisi kode saat ini:** `DashboardController::index()` sudah menghitung `total_penduduk`, `total_surat`, `surat_hari_ini`, `total_jenis`. Model `Penduduk` sudah punya kolom `jenis_kelamin` (enum `L`/`P`) dan `status_hubungan` (termasuk nilai `'Kepala Keluarga'`) — jadi datanya sudah tersedia, tinggal query tambahan.

**Rencana implementasi:**
1. Di `DashboardController::index()`, tambah ke array `$stats`:
   - `'total_laki' => Penduduk::where('jenis_kelamin', 'L')->count()`
   - `'total_perempuan' => Penduduk::where('jenis_kelamin', 'P')->count()`
   - `'total_kepala_keluarga' => Penduduk::where('status_hubungan', 'Kepala Keluarga')->count()`
2. Tambah 3 card baru di `resources/views/dashboard/index.blade.php`, mengikuti pola `feature-card` yang sudah dipakai untuk card statistik lain di halaman yang sama.

Ini yang paling sederhana dari 5 revisi — murni penambahan, tidak menyentuh logika yang sudah ada.

---

## 4. Export Excel "Laporan Adminduk"

**File referensi:** `ADMINDUK TAHUN 2024 SEMESTER 2.xlsx` (dari kantor desa). Sudah dibuka & dicek strukturnya — ini adalah format laporan administrasi kependudukan semesteran standar (mirip format resmi Dukcapil/Kemendagri), berisi 2 sheet:

**Sheet 1 ("Desember")** — rekap per kategori, tiap kategori format 4 kolom (No, Kategori, Laki-laki, Perempuan, Jumlah):
- Wilayah (total desa)
- Agama (Islam, Kristen, Katholik, Hindu, Budha, Khonghucu, Kepercayaan)
- Pendidikan (Tidak/Blm Sekolah s.d. Strata III — 10 kategori)
- Hubungan Keluarga / Kepala Rumah Tangga (Kepala Keluarga, Suami, Isteri, Anak, dst — 11 kategori)
- Golongan Darah (A, B, AB, O + rhesus, Tidak Tahu — 13 kategori)
- ~~Disabilitas~~ — **di-drop dari scope**, lihat keputusan di bawah
- Status Perkawinan (Belum Kawin, Kawin, Cerai Hidup, Cerai Mati)
- Pekerjaan — **~100 kategori pekerjaan resmi** (Belum/Tidak Bekerja, Mengurus RT, Pelajar/Mahasiswa, ... sampai Presiden, Wakil Presiden, dll — daftar baku ala Dukcapil)

**Sheet 2 ("usia")** — rekap jumlah penduduk per usia satuan tahun (0, 1, 2, ... dst) dikelompokkan Laki-laki/Perempuan/Jumlah, plus kolom tambahan yang tampak seperti rekap kelompok usia (kemungkinan cocok dengan konstanta `Penduduk::KELOMPOK_USIA` yang sudah ada di kode: balita/anak/remaja/dewasa/lansia).

**Keputusan final (menyederhanakan scope dibanding draft sebelumnya):**
- **Section Disabilitas tidak usah dibuat.** Tidak perlu tambah kolom `disabilitas` ke `Penduduk`, tidak perlu ubah form/import. Section ini cukup dilewati di file export (sheet langsung lanjut dari Golongan Darah ke Status Perkawinan, tanpa blok Disabilitas), atau ditulis sebagai blok kosong/nol kalau kantor desa masih mau baris judulnya tetap ada untuk konsistensi format — perlu dicek preferensinya saat serah terima, tapi bukan pekerjaan development.
- **Field `pekerjaan` tetap teks bebas** — tidak diubah jadi dropdown. Sudah dicek data riil desa (`bip_ 2019.xlsx`, 5.102 baris): 39 nilai unik yang dipakai operator selama ini **sudah persis memakai penulisan resmi ala Dukcapil** (`BELUM/TIDAK BEKERJA`, `PETANI/PEKEBUN`, `KARYAWAN SWASTA`, dst — semua uppercase, cocok dengan kategori di `ADMINDUK TAHUN 2024 SEMESTER 2.xlsx`). Jadi cukup `groupBy('pekerjaan')` langsung tanpa perlu tabel mapping/normalisasi tambahan. Nilai yang kebetulan tidak cocok/typo dengan daftar resmi otomatis masuk baris "PEKERJAAN LAINNYA" (kategori ini sudah ada di daftar resmi, jadi tidak perlu bikin baris baru).

**Rencana implementasi:**
1. Buat class baru `app/Exports/AdmindukExport.php` — karena `maatwebsite/excel` sudah jadi dependency (sudah dipakai untuk `PendudukExport`/`SuratExport`), pakai `WithMultipleSheets` (untuk 2 sheet) dan tiap sheet pakai `FromView` atau `FromArray` supaya bisa atur layout 2 tabel bersebelahan (kiri: wilayah/agama/dst, kanan: pekerjaan) seperti file aslinya — pakai styling merge-cell & border secukupnya biar mirip format asli.
2. Tambah tombol/route `GET /penduduk/export-adminduk` di `PendudukController`, mengikuti pola route export yang sudah ada (`penduduk/export`).
3. Query rekap pakai `groupBy` per kolom (`jenis_kelamin`, `agama`, `pendidikan`, `status_hubungan`, `golongan_darah`, `status_kawin`, `pekerjaan`) — semua sudah jadi kolom asli di `Penduduk`, tidak ada migration tambahan yang dibutuhkan.
4. Daftar kategori tiap section (nama & urutan persis) di-hardcode sesuai file `ADMINDUK TAHUN 2024 SEMESTER 2.xlsx`, supaya urutan baris di hasil export identik dengan contoh dari kantor desa — termasuk daftar ~100 kategori pekerjaan, dengan bucket "PEKERJAAN LAINNYA" menampung nilai yang tidak cocok ke 99 kategori resmi lainnya.
5. Sheet usia: pakai `tanggal_lahir` untuk hitung umur per baris (mirip logika `scopeFilter` kelompok usia yang sudah ada), lalu `groupBy` umur (tahun penuh) dan `jenis_kelamin`.

Dengan disabilitas di-drop dan pekerjaan tidak perlu normalisasi, poin ini jadi murni "replikasi layout Excel + query groupBy" — tidak ada lagi keputusan data yang mengganjal sebelum mulai coding.

---

## 5. Ganti Template Surat

File revisi ada di folder `Downloads/revisi`. Sudah dicek satu-satu dan dicocokkan ke `kode_surat` yang ada di `JenisSuratSeeder.php`:

| File dari kantor desa | Jenis surat tujuan (kode_surat) | Status |
|---|---|---|
| `SURAT KETERANGAN KEMATIAN.docx` | SKM | Cocok langsung, tinggal replace |
| `surat kehilangan.docx` | SKH | Cocok langsung, tinggal replace |
| `keterangan belum nikah.docx` | SKBM | Cocok langsung, tinggal replace |
| `SKCK BARU.docx` | SKCK | Cocok langsung, tinggal replace |
| `KETERANGAN USAHA.docx` | SKU | Cocok langsung, tinggal replace |
| `Surat Pernyataan Ada kepala desa.docx` | SPN | Jadi template utama (kop), lihat poin 1 |
| `SURAT PERNYATAAN tanpa kepala desa.docx` | SPN | Tidak perlu disimpan terpisah — sudah tercakup oleh fitur "tanpa kop" di poin 1 |
| `ijin keramaian ada hiburan.docx` / `IZIN KERAMAIAN tanpa hiburan.docx` | SIK | **Keputusan final:** 1 jenis surat, field dinamis "Ada Hiburan" (Ya/Tidak) — lihat detail teknis di bawah |
| `Ket. suami atau istri atau ibu pergi.docx` | Jenis surat baru | **Keputusan final:** dibuatkan entri Jenis Surat baru — lihat detail teknis di bawah |

**Rencana implementasi — 5 file yang cocok langsung:**
1. Ganti lewat menu **Jenis Surat → Edit → Upload Template** yang sudah ada di aplikasi (fitur ini sudah jadi, sesuai yang sudah dijelaskan ke kantor desa saat demo) — bukan pekerjaan development, murni operasional/data-entry oleh tim.
2. Pastikan setiap template baru sudah lolos `TemplateLogoService::applyToTemplate()` (otomatis jalan tiap upload template baru — logo desa/kabupaten ikut tertempel) dan placeholder di dalam `.docx` (`${...}`) sesuai dengan field yang didefinisikan di masing-masing Jenis Surat, supaya `SuratGeneratorService` bisa mengisi dengan benar. Perlu dicek manual satu per satu setelah upload (generate 1 contoh surat, bandingkan ke hasil yang diharapkan office).

**Rencana implementasi — SIK dengan field dinamis "Ada Hiburan":**

Sudah dibuka & dibandingkan isi kedua file aslinya. Bedanya bukan cuma satu kalimat, tapi satu blok tambahan penuh: versi "ada hiburan" punya **3 bagian** dalam satu dokumen (1: Surat Pengantar Izin Keramaian dari pemohon, 2: Surat Pernyataan pemohon soal keamanan/miras, 3: Surat Pernyataan dari panitia/organisasi penyelenggara hiburan — nama organisasi, ketua, wakil ketua, sekretaris, hari/tanggal/jam/tempat acara, jenis hiburan, pernyataan kesanggupan tertib), sedangkan versi "tanpa hiburan" cuma punya **2 bagian pertama** saja.

1. Satukan jadi 1 template dasar (pakai versi "ada hiburan" sebagai basis karena lebih lengkap), bungkus bagian ke-3 (blok pernyataan panitia hiburan) dengan penanda blok `${hiburan}` di awal dan `${/hiburan}` di akhir — ini format standar yang sudah dikenali `PhpOffice\PhpWord\TemplateProcessor::cloneBlock()` (`phpoffice/phpword` sudah jadi dependency aplikasi).
2. Tambah field dinamis baru di menu Jenis Surat untuk SIK: `ada_hiburan` (type baru: `select`, opsi Ya/Tidak) + field-field khusus blok hiburan (nama organisasi, nama ketua, wakil ketua, sekretaris, hari, tanggal, jam, tempat, jenis hiburan) — semua bertipe `text`, sudah didukung skema field dinamis yang ada.
3. **Field type `select` belum ada di sistem** (tipe yang ada sekarang: `text`, `number`, `date`, `textarea`, `anak_kk` — lihat `JenisSurat::additionalFields()` dan `renderFields()` di `surat/create.blade.php`/`edit.blade.php`). Perlu ditambah: render `<select>` dengan daftar opsi dari definisi field, dan (nice-to-have) JS untuk show/hide field-field blok hiburan tergantung pilihan `ada_hiburan`, supaya operator tidak bingung isi field yang tidak relevan.
4. Di `SuratGeneratorService::generate()`, setelah isi semua placeholder biasa, panggil `$processor->cloneBlock('hiburan', $data['ada_hiburan'] === 'Ya' ? 1 : 0)` — teknik bawaan PHPWord untuk menampilkan (clone 1x) atau menghilangkan (clone 0x) sebuah blok berdasarkan kondisi, tidak perlu manipulasi XML manual seperti di poin 1 (kop surat).
5. Kode klasifikasi SIK: dari nomor yang tertera di file asli (`470/538/409.40.13/2026`), kode klasifikasinya **470** — bisa langsung diisi di form Jenis Surat.

**Rencana implementasi — Jenis surat baru "Ket. Suami/Istri/Ibu Pergi Bekerja":**

Isi surat aslinya: data pemohon standar (nama, TTL, jenis kelamin, pekerjaan, agama, NIK, alamat) + keterangan tentang anggota keluarga (suami/istri/ibu) yang sedang pergi bekerja ke luar daerah/luar negeri + tujuan pembuatan surat (mis. "kelengkapan pengajuan KUR di bank"). Kode klasifikasi di contoh file juga **470**.

1. Buat entri Jenis Surat baru lewat menu yang sudah ada (tidak perlu development) — usul nama "Surat Keterangan Kepergian Anggota Keluarga" dan `kode_surat` baru mis. `SKKB` (belum dipakai jenis surat manapun saat ini), tapi nama final sebaiknya dikonfirmasi ke kantor desa saat serah terima supaya konsisten dengan istilah yang mereka pakai sehari-hari.
2. Field dinamis yang perlu didefinisikan: Hubungan Keluarga yang Pergi (`select`: Suami/Istri/Ibu — pakai field type `select` yang sama yang ditambahkan untuk SIK di atas), Nama yang Bersangkutan, Alamat/Dusun yang Bersangkutan, Tujuan Kepergian (kota/negara), Sejak Bulan/Tahun, dan Keperluan Surat (`textarea`, bebas isi mis. "kelengkapan pengajuan KUR di BRI").
3. Upload template `.docx` yang sudah dikirim kantor desa sebagai template jenis surat ini, sesuaikan placeholder `${...}` dengan nama field di atas.

Dengan `select` + `cloneBlock` ini terselesaikan, tidak ada lagi bagian dari poin 5 yang menunggu konfirmasi kantor desa — tinggal eksekusi.

---

## Urutan Pengerjaan yang Disarankan

Semua 5 poin sekarang sudah punya keputusan final — tidak ada lagi yang perlu dikonfirmasi ke kantor desa sebelum mulai kerja (kecuali nama tampilan untuk jenis surat baru di poin 5, yang sifatnya kosmetik dan bisa menyusul saat serah terima).

1. Poin 3 (dashboard) — paling cepat, kerjakan duluan sebagai quick win.
2. Poin 2 (nomor surat) — sebagian besar infrastrukturnya sudah ada, tinggal 1 kolom baru + form input + isi config.
3. Poin 4 (export Adminduk) — murni replikasi layout + query, bisa dikerjakan kapan saja.
4. Poin 5 (ganti template + jenis surat baru) — 5 file yang cocok langsung bisa diganti duluan (operasional). Kerjakan field type `select` di sini dulu, karena field type ini juga jadi prasyarat teknis untuk poin 1 (SIK) dan jenis surat baru.
5. Poin 1 (kop/tanpa kop) — dikerjakan setelah field `select` selesai; ini paling butuh kehati-hatian karena manipulasi XML docx (`cloneBlock` untuk blok hiburan SIK + hapus tabel kop terprogram), meski pola teknisnya sudah ada contohnya di `TemplateLogoService`.

Setelah 5 poin ini selesai dan sudah dicek ulang bareng kantor desa, project bisa dianggap final — tidak perlu menambah modul lain di luar yang diminta saat demo.
