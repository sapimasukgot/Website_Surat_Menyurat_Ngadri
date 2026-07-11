<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePejabatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kepala_desa' => ['required', 'string', 'max:150'],
            'sekretaris_desa' => ['required', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'kepala_desa.required' => 'Nama Kepala Desa wajib diisi.',
            'sekretaris_desa.required' => 'Nama Sekretaris Desa wajib diisi.',
        ];
    }
}
