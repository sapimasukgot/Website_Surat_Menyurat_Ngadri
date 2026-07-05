<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
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
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'template' => [
                'nullable', 'file', 'max:5120', 'mimes:docx',
                'mimetypes:application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
            'fields' => ['nullable', 'array'],
            'fields.*.label' => ['nullable', 'string', 'max:100'],
            'fields.*.type' => ['nullable', Rule::in(['text', 'textarea', 'date', 'number'])],
            'fields.*.required' => ['nullable'],
        ];
    }

    public function normalizedFields(): array
    {
        return collect($this->input('fields', []))
            ->filter(fn ($f) => filled($f['label'] ?? null))
            ->map(fn ($f) => [
                'name' => Str::slug($f['label'], '_'),
                'label' => trim($f['label']),
                'type' => $f['type'] ?? 'text',
                'required' => (bool) ($f['required'] ?? false),
            ])
            ->values()
            ->all();
    }

    public function messages(): array
    {
        return [
            'template.mimes' => 'Template harus berupa berkas Word (.docx).',
            'kode_surat.unique' => 'Kode surat sudah digunakan.',
        ];
    }
}
