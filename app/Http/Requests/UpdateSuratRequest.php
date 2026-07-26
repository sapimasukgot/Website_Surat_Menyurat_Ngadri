<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
}
