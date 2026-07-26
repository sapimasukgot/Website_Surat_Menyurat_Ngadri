<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreJenisSuratRequest;
use App\Http\Requests\UpdateJenisSuratRequest;
use App\Models\JenisSurat;
use App\Services\TemplateLogoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JenisSuratController extends Controller
{
    private const TEMPLATE_DIR = 'templates';

    public function index(Request $request): View
    {
        $jenisSurats = JenisSurat::query()
            ->when($request->get('q'), fn ($q, $term) => $q->where('nama_surat', 'like', "%{$term}%")
                ->orWhere('kode_surat', 'like', "%{$term}%"))
            ->withCount('surats')
            ->orderBy('nama_surat')
            ->paginate(15)
            ->withQueryString();

        return view('jenis_surat.index', compact('jenisSurats'));
    }

    public function create(): View
    {
        return view('jenis_surat.create', ['jenisSurat' => new JenisSurat()]);
    }

    public function store(StoreJenisSuratRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['nama_surat', 'kode_surat', 'kode_klasifikasi', 'deskripsi']);
        $data['is_active'] = $request->boolean('is_active');
        $data['fields'] = $request->normalizedFields();

        if ($request->hasFile('template')) {
            $data = array_merge($data, $this->storeTemplate($request->file('template')));
        }

        JenisSurat::create($data);

        return redirect()->route('jenis-surat.index')->with('success', 'Jenis surat berhasil ditambahkan.');
    }

    public function edit(JenisSurat $jenisSurat): View
    {
        return view('jenis_surat.edit', compact('jenisSurat'));
    }

    public function update(UpdateJenisSuratRequest $request, JenisSurat $jenisSurat): RedirectResponse
    {
        $data = $request->safe()->only(['nama_surat', 'kode_surat', 'kode_klasifikasi', 'deskripsi']);
        $data['is_active'] = $request->boolean('is_active');
        $data['fields'] = $request->normalizedFields();

        if ($request->hasFile('template')) {

            if ($jenisSurat->template_path) {
                Storage::disk('public')->delete($jenisSurat->template_path);
            }
            $data = array_merge($data, $this->storeTemplate($request->file('template')));
        }

        $jenisSurat->update($data);

        return redirect()->route('jenis-surat.index')->with('success', 'Jenis surat berhasil diperbarui.');
    }

    public function destroy(JenisSurat $jenisSurat): RedirectResponse
    {
        if ($jenisSurat->surats()->exists()) {
            return back()->with('error', 'Jenis surat tidak dapat dihapus karena sudah digunakan pada riwayat surat.');
        }

        if ($jenisSurat->template_path) {
            Storage::disk('public')->delete($jenisSurat->template_path);
        }

        $jenisSurat->delete();

        return redirect()->route('jenis-surat.index')->with('success', 'Jenis surat berhasil dihapus.');
    }

    public function downloadTemplate(JenisSurat $jenisSurat): StreamedResponse
    {
        abort_unless($jenisSurat->hasTemplate(), 404, 'Template tidak ditemukan.');

        return Storage::disk('public')->download(
            $jenisSurat->template_path,
            $jenisSurat->template_original_name ?? 'template.docx'
        );
    }

    private function storeTemplate(\Illuminate\Http\UploadedFile $file): array
    {
        $safeName = Str::random(20).'.docx';
        $path = $file->storeAs(self::TEMPLATE_DIR, $safeName, 'public');

        // Terapkan logo kop (jika sudah diatur) ke template yang baru diunggah.
        try {
            app(TemplateLogoService::class)->applyToTemplate(Storage::disk('public')->path($path));
        } catch (\Throwable $e) {
            // Template tetap tersimpan walau logo gagal diterapkan.
        }

        return [
            'template_path' => $path,
            'template_original_name' => $file->getClientOriginalName(),
        ];
    }
}
