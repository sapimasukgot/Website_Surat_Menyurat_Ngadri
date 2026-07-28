<?php
namespace App\Http\Requests;

use App\Models\Surat;
use App\Services\NomorSuratService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rapikan nomor surat SEBELUM divalidasi, supaya aturan unique dan pengecekan
     * nomor urut di bawah memeriksa nilai yang sama persis dengan yang nanti
     * disimpan (mis. "470 / 212 // 2026" → "470/212/2026").
     */
    protected function prepareForValidation(): void
    {
        $nomor = $this->input('nomor_surat');

        if (is_string($nomor)) {
            $this->merge(['nomor_surat' => app(NomorSuratService::class)->rapikan($nomor)]);
        }
    }

    public function rules(): array
    {
        $surat = $this->route('surat');

        $rules = [
            'nomor_surat' => ['required', 'string', 'max:100', Rule::unique('surats', 'nomor_surat')->ignore($surat->id)],
            'tanggal_surat' => ['required', 'date'],
            'penandatangan_role' => ['nullable', Rule::in(['kepala_desa', 'sekretaris_desa'])],
            'pakai_kop' => ['nullable', 'boolean'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'data' => ['sometimes', 'array'],
            'data.*' => ['nullable', 'string', 'max:2000'],
        ];

        // Field bertipe dropdown tetap dibatasi ke daftar pilihannya, sama
        // seperti saat surat dibuat — penting karena sebagian dipakai sebagai
        // saklar blok pada template.
        foreach ($surat->jenisSurat?->additionalFields() ?? [] as $field) {
            if ($field['type'] === 'select' && filled($field['options'])) {
                $rules['data.'.$field['name']] = ['nullable', Rule::in($field['options'])];
            }
        }

        return $rules;
    }

    /**
     * Nomor urut kini satu urutan berjalan untuk seluruh surat keluar, jadi angka
     * yang sama tidak boleh dipakai dua surat pada tahun yang sama — walaupun
     * kode klasifikasinya berbeda sehingga nomor lengkapnya kelihatan tidak sama.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $surat = $this->route('surat');
            $nomor = $this->input('nomor_surat');
            $tanggal = $this->input('tanggal_surat');

            // Lewati kalau input dasarnya sendiri sudah gagal divalidasi — pesan
            // error yang relevan sudah muncul dari aturan di rules().
            if ($surat === null || !is_string($nomor) || $nomor === '' || !is_string($tanggal)
                || $validator->errors()->hasAny(['nomor_surat', 'tanggal_surat'])) {
                return;
            }

            $service = app(NomorSuratService::class);

            // Nomor tidak diubah operator → jangan diganggu. Penting untuk surat
            // lama dari penomoran versi sebelumnya (yang dihitung per jenis surat),
            // karena di data lama angka urut yang sama bisa muncul di dua surat
            // dengan kode klasifikasi berbeda. Tanpa pengecualian ini, surat-surat
            // itu tidak akan bisa disunting sama sekali.
            if ($nomor === $service->rapikan((string) $surat->nomor_surat)) {
                return;
            }

            try {
                $tahun = Carbon::parse($tanggal)->year;
            } catch (\Throwable $e) {
                return;
            }

            $urut = $service->bacaUrut($nomor, $surat->jenisSurat);

            if ($urut === null) {
                return;
            }

            if ($urut < 1 || $urut > 99999) {
                $validator->errors()->add(
                    'nomor_surat',
                    'Nomor urut '.$urut.' tidak wajar. Periksa kembali susunan nomor surat — '
                        .'nomor urut adalah angka urutan surat keluar, bukan tahun atau kode.'
                );

                return;
            }

            $bentrok = Surat::withTrashed()
                ->where('id', '!=', $surat->id)
                ->where('nomor_urut', $urut)
                ->whereYear('tanggal_surat', $tahun)
                ->first();

            if ($bentrok !== null) {
                $validator->errors()->add(
                    'nomor_surat',
                    'Nomor urut '.$urut.' sudah dipakai surat lain tahun ini ('.$bentrok->nomor_surat
                        .($bentrok->trashed() ? ', surat tersebut sudah dihapus' : '')
                        .'). Nomor urut berjalan bersama untuk semua jenis surat, jadi tidak boleh kembar.'
                );
            }
        });
    }
}
