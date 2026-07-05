<?php
namespace App\Http\Requests;

use App\Models\JenisSurat;
use Illuminate\Foundation\Http\FormRequest;

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
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ];

        $jenis = JenisSurat::find($this->input('jenis_surat_id'));

        if ($jenis) {
            foreach ($jenis->additionalFields() as $field) {
                $rules['data.'.$field['name']] = $field['required']
                    ? ['required', 'string', 'max:1000']
                    : ['nullable', 'string', 'max:1000'];
            }
        }

        return $rules;
    }
}
