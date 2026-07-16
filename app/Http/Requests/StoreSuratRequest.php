<?php
namespace App\Http\Requests;

use App\Models\JenisSurat;
use App\Models\Penduduk;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'jenis_surat_id' => ['required', 'exists:jenis_surats,id'],
            'penduduk_id' => ['required', 'exists:penduduks,id'],
            'tanggal_surat' => ['required', 'date'],
            'penandatangan_role' => ['nullable', Rule::in(['kepala_desa', 'sekretaris_desa'])],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ];

        $jenis = JenisSurat::find($this->input('jenis_surat_id'));

        if ($jenis) {
            foreach ($jenis->additionalFields() as $field) {
                if ($field['type'] === 'anak_kk') {
                    $rules['data.'.$field['name']] = [
                        $field['required'] ? 'required' : 'nullable',
                        'integer',
                        Rule::exists('penduduks', 'id'),
                        $this->anakSatuKkRule(),
                    ];

                    continue;
                }

                $rules['data.'.$field['name']] = $field['required']
                    ? ['required', 'string', 'max:1000']
                    : ['nullable', 'string', 'max:1000'];
            }
        }

        return $rules;
    }

    /**
     * Anak yang dipilih wajib berada dalam satu KK dengan pemohon.
     */
    private function anakSatuKkRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) {
            $pemohon = Penduduk::find($this->input('penduduk_id'));
            $anak = Penduduk::find($value);

            if (! $pemohon || ! $anak || blank($pemohon->no_kk) || $anak->no_kk !== $pemohon->no_kk) {
                $fail('Anak yang dipilih harus berada dalam satu KK dengan pemohon.');
            }
        };
    }
}
