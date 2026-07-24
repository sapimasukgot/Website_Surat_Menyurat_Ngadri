<?php
namespace App\Http\Controllers;

use App\Exports\SuratExport;
use App\Http\Requests\ImportSuratRequest;
use App\Http\Requests\StoreSuratRequest;
use App\Http\Requests\UpdateSuratRequest;
use App\Models\ImportLog;
use App\Models\JenisSurat;
use App\Models\Penduduk;
use App\Models\Setting;
use App\Models\Surat;
use App\Services\NomorSuratService;
use App\Services\SuratGeneratorService;
use App\Services\SuratImportService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratController extends Controller
{
    private const SORTABLE = ['nomor_surat', 'tanggal_surat', 'created_at'];

    public function __construct(
        private readonly NomorSuratService $nomorService,
        private readonly SuratGeneratorService $generator,
    ) {}

    public function index(Request $request): View
    {
        $sort = in_array($request->get('sort'), self::SORTABLE, true) ? $request->get('sort') : 'created_at';
        $direction = $request->get('direction') === 'asc' ? 'asc' : 'desc';

        $surats = Surat::query()
            ->with(['jenisSurat', 'penduduk', 'user'])
            ->search($request->get('q'))
            ->when($request->get('jenis_surat_id'), fn ($q, $v) => $q->where('jenis_surat_id', $v))
            ->when($request->get('dari'), fn ($q, $v) => $q->whereDate('tanggal_surat', '>=', $v))
            ->when($request->get('sampai'), fn ($q, $v) => $q->whereDate('tanggal_surat', '<=', $v))
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        $jenisList = JenisSurat::orderBy('nama_surat')->get(['id', 'nama_surat']);

        return view('surat.index', compact('surats', 'jenisList', 'sort', 'direction'));
    }

    /**
     * Backup riwayat surat ke Excel (mengikuti filter pencarian yang aktif).
     */
    public function export(Request $request): BinaryFileResponse
    {
        $filename = 'riwayat-surat-'.now()->format('Ymd-His').'.xlsx';

        return Excel::download(
            new SuratExport($request->only(['q', 'jenis_surat_id', 'dari', 'sampai'])),
            $filename
        );
    }

    /**
     * Backup seluruh berkas Word (.docx) riwayat surat sebagai satu arsip ZIP.
     * Berkas yang belum pernah dibuat / hilang akan di-generate terlebih dahulu.
     */
    public function exportZip(Request $request): mixed
    {
        $surats = Surat::query()
            ->with(['jenisSurat', 'penduduk'])
            ->search($request->get('q'))
            ->when($request->get('jenis_surat_id'), fn ($q, $v) => $q->where('jenis_surat_id', $v))
            ->when($request->get('dari'), fn ($q, $v) => $q->whereDate('tanggal_surat', '>=', $v))
            ->when($request->get('sampai'), fn ($q, $v) => $q->whereDate('tanggal_surat', '<=', $v))
            ->orderBy('tanggal_surat')
            ->orderBy('id')
            ->get();

        if ($surats->isEmpty()) {
            return back()->with('error', 'Tidak ada surat pada filter yang dipilih.');
        }

        $zipPath = storage_path('app/riwayat-surat-'.now()->format('Ymd-His').'.zip');
        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat berkas ZIP.');
        }

        $berhasil = 0;
        $gagal = [];

        foreach ($surats as $surat) {
            try {
                if (! $surat->hasFile()) {
                    $surat->update(['file_path' => $this->generator->generate($surat)]);
                }

                $zip->addFile(
                    Storage::disk('public')->path($surat->file_path),
                    $this->zipEntryName($surat)
                );
                $berhasil++;
            } catch (\Throwable $e) {
                $gagal[] = $surat->nomor_surat.' — '.$e->getMessage();
            }
        }

        if ($gagal !== []) {
            $zip->addFromString(
                'CATATAN-GAGAL.txt',
                "Surat yang gagal disertakan:\n".implode("\n", $gagal)."\n"
            );
        }

        $zip->close();

        if ($berhasil === 0) {
            @unlink($zipPath);

            return back()->with('error', 'Tidak ada berkas surat yang berhasil dibuat. Periksa template jenis surat.');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    private function zipEntryName(Surat $surat): string
    {
        $nomor = str_replace('/', '-', $surat->nomor_surat);
        $nama = strtoupper(Str::slug($surat->penduduk->nama_lengkap ?? 'pemohon', ' '));

        return trim($nomor.' - '.$nama).'.docx';
    }

    public function importForm(): View
    {
        $lastLog = ImportLog::with('user')->context('surat')->latest()->first();

        return view('surat.import', compact('lastLog'));
    }

    public function import(ImportSuratRequest $request, SuratImportService $service): RedirectResponse
    {
        $log = $service->handle($request->file('file'), $request->user());

        return redirect()->route('surat.import.form')->with('success', sprintf(
            'Restore riwayat surat selesai. Baru: %d, diperbarui: %d, gagal: %d.',
            $log->inserted_count,
            $log->updated_count,
            $log->failed_count
        ));
    }

    public function create(): View
    {
        return view('surat.create', [
            'jenisList' => JenisSurat::where('is_active', true)->orderBy('nama_surat')
                ->get(['id', 'nama_surat', 'kode_surat', 'fields']),
            'penduduks' => Penduduk::orderBy('nama_lengkap')->get(['id', 'nik', 'nama_lengkap']),
            'penandatanganList' => $this->penandatanganOptions(),
        ]);
    }

    public function store(StoreSuratRequest $request): RedirectResponse
    {
        $jenis = JenisSurat::findOrFail($request->integer('jenis_surat_id'));
        $penduduk = Penduduk::findOrFail($request->integer('penduduk_id'));
        $tanggal = Carbon::parse($request->input('tanggal_surat'));
        $role = $request->input('penandatangan_role', 'kepala_desa');

        $surat = Surat::create([
            'nomor_surat' => $this->nomorService->generate($jenis, $tanggal),
            'jenis_surat_id' => $jenis->id,
            'penduduk_id' => $penduduk->id,
            'user_id' => $request->user()->id,
            'tanggal_surat' => $tanggal,
            'data_surat' => $this->buildSnapshot($penduduk, $jenis, $request->input('data', []), $role),
        ]);

        return redirect()->route('surat.edit', $surat)
            ->with('success', 'Draft surat dibuat. Periksa & sunting isi sebelum membuat berkas final.');
    }

    public function show(Surat $surat): View
    {
        $surat->load(['jenisSurat', 'penduduk', 'user']);

        return view('surat.show', compact('surat'));
    }

    public function edit(Surat $surat): View
    {
        $surat->load(['jenisSurat', 'penduduk']);

        return view('surat.edit', [
            'surat' => $surat,
            'penandatanganList' => $this->penandatanganOptions(),
        ]);
    }

    public function update(UpdateSuratRequest $request, Surat $surat): RedirectResponse
    {
        $role = $request->input('penandatangan_role', 'kepala_desa');
        $signer = Setting::penandatangan($role);

        $data = array_merge($request->input('data', []), [
            'penandatangan' => $signer['nama'],
            'jabatan_ttd' => $signer['jabatan'],
            'penandatangan_role' => $role,
        ]);

        $surat->update([
            'nomor_surat' => $request->input('nomor_surat'),
            'tanggal_surat' => Carbon::parse($request->input('tanggal_surat')),
            'data_surat' => $data,
        ]);

        return redirect()->route('surat.show', $surat)->with('success', 'Isi surat berhasil disimpan.');
    }

    public function generate(Surat $surat): RedirectResponse
    {
        try {
            $path = $this->generator->generate($surat);
            $surat->update(['file_path' => $path]);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('surat.show', $surat)->with('success', 'Berkas Word (.docx) berhasil dibuat.');
    }

    public function download(Surat $surat): StreamedResponse
    {
        if (! $surat->hasFile()) {
            $surat->update(['file_path' => $this->generator->generate($surat)]);
        }

        return Storage::disk('public')->download($surat->file_path, $this->filename($surat, 'docx'));
    }

    public function print(Surat $surat): mixed
    {
        try {
            $surat->update(['file_path' => $this->generator->generate($surat)]);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return view('surat.print', [
            'surat' => $surat,
            'docxUrl' => route('surat.download', $surat),
        ]);
    }

    public function destroy(Surat $surat): RedirectResponse
    {
        if ($surat->file_path) {
            Storage::disk('public')->delete($surat->file_path);
        }

        $surat->delete();

        return redirect()->route('surat.index')->with('success', 'Surat berhasil dihapus.');
    }

    private function buildSnapshot(Penduduk $p, JenisSurat $jenis, array $additional, string $role = 'kepala_desa'): array
    {
        $signer = Setting::penandatangan($role);
        $additional = $this->expandAnakFields($jenis, $additional);

        $base = [
            'nama' => $p->nama_lengkap,
            'nik' => $p->nik,
            'no_kk' => $p->no_kk,
            'tempat_lahir' => $p->tempat_lahir,
            'tanggal_lahir' => $p->tanggal_lahir?->format('d-m-Y'),
            'jenis_kelamin' => $p->jenis_kelamin_label,
            'golongan_darah' => $p->golongan_darah,
            'agama' => $p->agama,
            'pendidikan' => $p->pendidikan,
            'pekerjaan' => $p->pekerjaan,
            'status_kawin' => $p->status_kawin,
            'status_hubungan' => $p->status_hubungan,
            'nama_ayah' => $p->nama_ayah,
            'nama_ibu' => $p->nama_ibu,
            'alamat' => $p->alamat,
            'rt' => $p->rt,
            'rw' => $p->rw,
            'dusun' => $p->dusun,
            'no_hp' => $p->no_hp,
        ];

        $signature = [
            'penandatangan' => $signer['nama'],
            'jabatan_ttd' => $signer['jabatan'],
            'penandatangan_role' => $role,
        ];

        return array_merge($base, array_filter($additional, fn ($v) => $v !== null && $v !== ''), $signature);
    }

    /**
     * Field bertipe anak_kk berisi ID penduduk (anak). Diubah menjadi
     * kumpulan placeholder siap pakai: ${anak}, ${nama_anak}, ${nik_anak},
     * ${tempat_lahir_anak}, ${tanggal_lahir_anak}, ${jenis_kelamin_anak}.
     */
    private function expandAnakFields(JenisSurat $jenis, array $additional): array
    {
        foreach ($jenis->additionalFields() as $field) {
            if ($field['type'] !== 'anak_kk') {
                continue;
            }

            $name = $field['name'];
            $anakId = $additional[$name] ?? null;
            unset($additional[$name]);

            $anak = $anakId ? Penduduk::find($anakId) : null;

            if (! $anak) {
                continue;
            }

            $additional[$name] = "{$anak->nama_lengkap} ({$anak->nik})";
            $additional["nama_{$name}"] = $anak->nama_lengkap;
            $additional["nik_{$name}"] = $anak->nik;
            $additional["tempat_lahir_{$name}"] = $anak->tempat_lahir;
            $additional["tanggal_lahir_{$name}"] = $anak->tanggal_lahir?->format('d-m-Y');
            $additional["jenis_kelamin_{$name}"] = $anak->jenis_kelamin_label;
        }

        return $additional;
    }

    private function penandatanganOptions(): array
    {
        return [
            'kepala_desa' => Setting::get('kepala_desa', ''),
            'sekretaris_desa' => Setting::get('sekretaris_desa', ''),
        ];
    }

    private function filename(Surat $surat, string $ext = 'docx'): string
    {
        return str_replace('/', '-', $surat->nomor_surat).'.'.$ext;
    }
}
