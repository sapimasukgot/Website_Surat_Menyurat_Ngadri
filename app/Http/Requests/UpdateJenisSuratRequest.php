<?php
namespace App\Http\Requests;

use App\Models\JenisSurat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJenisSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('jenisSurat')->id;

        return [
            'nama_surat' => ['required', 'string', 'max:150'],
            'kode_surat' => ['required', 'string', 'max:20', Rule::unique('jenis_surats', 'kode_surat')->ignore($id)->whereNull('deleted_at')],
            // Hanya angka dan titik (mis. 470, 422.5). Garis miring dilarang karena
            // akan merusak susunan segmen nomor surat.
            'kode_klasifikasi' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+(\.[0-9]+)*$/'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'template' => [
                'nullable', 'file', 'max:5120', 'mimes:docx',
                'mimetypes:application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
            'fields' => ['nullable', 'array'],
            'fields.*.name' => ['nullable', 'string', 'max:100'],
            'fields.*.label' => ['nullable', 'string', 'max:100'],
            'fields.*.type' => ['nullable', Rule::in(JenisSurat::FIELD_TYPES)],
            'fields.*.required' => ['nullable'],
            'fields.*.options' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function normalizedFields(): array
    {
        return JenisSurat::normalizeFieldSchema($this->input('fields', []));
    }
}
