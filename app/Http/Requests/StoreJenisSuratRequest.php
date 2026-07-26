<?php
namespace App\Http\Requests;

use App\Models\JenisSurat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJenisSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_surat' => ['required', 'string', 'max:150'],
            'kode_surat' => ['required', 'string', 'max:20', Rule::unique('jenis_surats', 'kode_surat')->whereNull('deleted_at')],
            'kode_klasifikasi' => ['nullable', 'string', 'max:20'],
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

    public function messages(): array
    {
        return [
            'template.mimes' => 'Template harus berupa berkas Word (.docx).',
            'kode_surat.unique' => 'Kode surat sudah digunakan.',
        ];
    }
}
