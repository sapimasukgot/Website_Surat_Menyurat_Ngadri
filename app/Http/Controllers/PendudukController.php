<?php
namespace App\Http\Controllers;

use App\Exports\PendudukExport;
use App\Http\Requests\ImportPendudukRequest;
use App\Http\Requests\StorePendudukRequest;
use App\Http\Requests\UpdatePendudukRequest;
use App\Models\ImportLog;
use App\Models\Penduduk;
use App\Services\PendudukImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PendudukController extends Controller
{

    private const SORTABLE = ['nama_lengkap', 'nik', 'dusun', 'created_at'];

    public function index(Request $request): View
    {
        $sort = in_array($request->get('sort'), self::SORTABLE, true) ? $request->get('sort') : 'nama_lengkap';
        $direction = $request->get('direction') === 'desc' ? 'desc' : 'asc';

        $penduduks = Penduduk::query()
            ->search($request->get('q'))
            ->filter($request->only(['dusun', 'rt', 'rw', 'jenis_kelamin', 'agama']))
            ->orderBy($sort, $direction)
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        $dusunList = Penduduk::query()->whereNotNull('dusun')->distinct()->orderBy('dusun')->pluck('dusun');

        return view('penduduk.index', compact('penduduks', 'sort', 'direction', 'dusunList'));
    }

    public function create(): View
    {
        return view('penduduk.create', ['penduduk' => new Penduduk()]);
    }

    public function store(StorePendudukRequest $request): RedirectResponse
    {
        Penduduk::create($request->validated());

        return redirect()->route('penduduk.index')->with('success', 'Data penduduk berhasil ditambahkan.');
    }

    public function show(Penduduk $penduduk): View
    {
        $penduduk->load('surats.jenisSurat');

        return view('penduduk.show', compact('penduduk'));
    }

    /**
     * Daftar anak dalam satu KK dengan penduduk (untuk dropdown pada form surat).
     */
    public function keluarga(Penduduk $penduduk): JsonResponse
    {
        return response()->json([
            'no_kk' => $penduduk->no_kk,
            'anak' => $penduduk->anakSatuKk()->map(fn (Penduduk $a) => [
                'id' => $a->id,
                'nik' => $a->nik,
                'nama' => $a->nama_lengkap,
                'jenis_kelamin' => $a->jenis_kelamin_label,
                'tanggal_lahir' => $a->tanggal_lahir?->format('d-m-Y'),
            ])->values(),
        ]);
    }

    public function edit(Penduduk $penduduk): View
    {
        return view('penduduk.edit', compact('penduduk'));
    }

    public function update(UpdatePendudukRequest $request, Penduduk $penduduk): RedirectResponse
    {
        $penduduk->update($request->validated());

        return redirect()->route('penduduk.index')->with('success', 'Data penduduk berhasil diperbarui.');
    }

    public function destroy(Penduduk $penduduk): RedirectResponse
    {
        $penduduk->delete();

        return redirect()->route('penduduk.index')->with('success', 'Data penduduk berhasil dihapus.');
    }

    public function importForm(): View
    {
        $lastLog = ImportLog::with('user')->latest()->first();

        return view('penduduk.import', compact('lastLog'));
    }

    public function import(ImportPendudukRequest $request, PendudukImportService $service): RedirectResponse
    {
        $log = $service->handle($request->file('file'), $request->user());

        return redirect()->route('penduduk.import.form')->with('success', sprintf(
            'Import selesai. Data baru: %d, diperbarui: %d, gagal: %d.',
            $log->inserted_count,
            $log->updated_count,
            $log->failed_count
        ))->with('import_log_id', $log->id);
    }

    public function export(): BinaryFileResponse
    {
        $filename = 'data-penduduk-'.now()->format('Ymd-His').'.xlsx';

        return Excel::download(new PendudukExport(), $filename);
    }
}
