<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:xlsx,xls',
                'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'Berkas harus berformat Excel (.xlsx atau .xls).',
            'file.max' => 'Ukuran berkas maksimal 10 MB.',
        ];
    }
}
