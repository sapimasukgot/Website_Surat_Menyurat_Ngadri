<?php

namespace App\Http\Controllers;

<<<<<<< Updated upstream
use App\Http\Requests\UpdatePejabatRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
=======
use App\Http\Requests\UpdateLogoRequest;
use App\Http\Requests\UpdatePejabatRequest;
use App\Models\Setting;
use App\Services\TemplateLogoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
>>>>>>> Stashed changes

class SettingController extends Controller
{
    public function updatePejabat(UpdatePejabatRequest $request): RedirectResponse
    {
        Setting::set('kepala_desa', $request->input('kepala_desa'));
        Setting::set('sekretaris_desa', $request->input('sekretaris_desa'));

        return back()->with('success', 'Nama perangkat desa berhasil diperbarui.');
    }
<<<<<<< Updated upstream
=======

    /**
     * Simpan logo kabupaten/desa lalu terapkan ke kop seluruh template surat.
     */
    public function updateLogo(UpdateLogoRequest $request, TemplateLogoService $service): RedirectResponse
    {
        foreach (['logo_kabupaten', 'logo_desa'] as $key) {
            if (! $request->hasFile($key)) {
                continue;
            }

            $path = 'logo/'.$key.'.png';
            Storage::disk('public')->put($path, $this->toPng($request->file($key), $key));
            Setting::set($key, $path);
        }

        $result = $service->applyToAllTemplates();

        $message = "Logo tersimpan dan diterapkan ke {$result['updated']} template surat.";

        if ($result['skipped'] !== []) {
            $message .= ' Dilewati: '.implode(', ', $result['skipped']).'.';
        }

        return back()->with('success', $message);
    }

    /**
     * Seragamkan format logo menjadi PNG agar aman disematkan ke berkas Word.
     */
    private function toPng(UploadedFile $file, string $key): string
    {
        $contents = $file->get();

        if ($file->getMimeType() === 'image/png') {
            return $contents;
        }

        if (! extension_loaded('gd')) {
            throw ValidationException::withMessages([
                $key => 'Ekstensi PHP GD tidak aktif. Silakan unggah logo dalam format PNG.',
            ]);
        }

        $image = imagecreatefromstring($contents);

        if ($image === false) {
            throw ValidationException::withMessages([$key => 'Berkas gambar tidak valid.']);
        }

        imagesavealpha($image, true);

        ob_start();
        imagepng($image);
        imagedestroy($image);

        return (string) ob_get_clean();
    }
>>>>>>> Stashed changes
}
