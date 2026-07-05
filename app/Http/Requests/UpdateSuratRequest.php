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
        $id = $this->route('surat')->id;

        return [
            'nomor_surat' => ['required', 'string', 'max:100', Rule::unique('surats', 'nomor_surat')->ignore($id)],
            'tanggal_surat' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'data' => ['required', 'array'],
            'data.*' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
