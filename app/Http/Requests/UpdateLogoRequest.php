<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logo_kabupaten' => ['nullable', 'required_without:logo_desa', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'logo_desa' => ['nullable', 'required_without:logo_kabupaten', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo_kabupaten.required_without' => 'Pilih minimal satu logo yang akan diubah.',
            'logo_desa.required_without' => 'Pilih minimal satu logo yang akan diubah.',
            '*.image' => 'Berkas harus berupa gambar.',
            '*.mimes' => 'Format logo harus PNG atau JPG.',
            '*.max' => 'Ukuran logo maksimal 2 MB.',
        ];
    }
}
