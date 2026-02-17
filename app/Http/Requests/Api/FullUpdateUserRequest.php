<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FullUpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->route('user');
        if (is_string($user)) {
            $user = \App\Models\User::find($user);
        }

        return $this->user()->can('update', $user);
    }

    /**
     * Get the validation rules that apply to the request.
     * PUT = full update, semua field wajib diisi.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->route('user'))
            ],
            'password' => ['required', 'string', 'min:8'],
            'role' => [
                'required',
                'string',
                'in:admin,user',
                // Only admin can update roles
                function ($attribute, $value, $fail) {
                    $userToUpdate = $this->route('user');
                    if (is_string($userToUpdate)) {
                        $userToUpdate = \App\Models\User::find($userToUpdate);
                    }

                    if (!$userToUpdate) {
                        return; // Controller will handle 404
                    }

                    // Hanya admin yang bisa ubah role
                    if ($value && !$this->user()->isAdmin()) {
                        $fail('Hanya admin yang boleh mengubah role.');
                        return;
                    }

                    // Cek jika role benar-benar berubah
                    if ($value && $value !== $userToUpdate->role) {
                        // Tidak boleh ubah role sendiri
                        if ($userToUpdate->id === $this->user()->id) {
                            $fail('Anda tidak boleh mengubah role Anda sendiri.');
                            return;
                        }

                        // Jika mau downgrade ke 'user', pastikan bukan admin terakhir
                        if ($value === 'user' && \App\Models\User::where('role', 'admin')->count() <= 1) {
                            $fail('Tidak bisa menurunkan role admin terakhir.');
                        }
                    }
                }
            ],
        ];
    }
}
