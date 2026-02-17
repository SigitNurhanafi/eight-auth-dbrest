<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SearchDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Bisa diakses oleh user yang sudah login (checked in routes)
    }

    /**
     * Get the validation rules that apply to the request.
     * Menggunakan format string pipe sesuai request user.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => 'sometimes|nullable|string|min:3',
            'nim' => 'sometimes|nullable|numeric',
            'ymd' => 'sometimes|nullable|numeric|digits:8',
        ];
    }

    /**
     * Custom messages untuk validasi.
     */
    public function messages(): array
    {
        return [
            'nama.min' => 'Pencarian nama minimal harus 3 karakter.',
            'nim.numeric' => 'NIM harus berupa angka.',
            'ymd.digits' => 'Format tanggal (ymd) harus 8 digit (contoh: 20230405).',
        ];
    }
}
