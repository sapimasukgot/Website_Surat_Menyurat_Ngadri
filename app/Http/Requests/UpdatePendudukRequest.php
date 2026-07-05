<?php
namespace App\Http\Requests;

use App\Models\Penduduk;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePendudukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('penduduk')->id;

        return [
            'nik' => ['required', 'digits:16', Rule::unique('penduduks', 'nik')->ignore($id)->whereNull('deleted_at')],
            'no_kk' => ['required', 'digits:16'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['required', Rule::in(array_keys(Penduduk::JENIS_KELAMIN))],
            'agama' => ['nullable', 'string', 'max:50'],
            'pendidikan' => ['nullable', 'string', 'max:50'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'status_kawin' => ['required', Rule::in(Penduduk::STATUS_KAWIN)],
            'alamat' => ['required', 'string', 'max:500'],
            'rt' => ['nullable', 'string', 'max:3'],
            'rw' => ['nullable', 'string', 'max:3'],
            'dusun' => ['nullable', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nik' => preg_replace('/\D/', '', (string) $this->nik),
            'no_kk' => preg_replace('/\D/', '', (string) $this->no_kk),
        ]);
    }
}
