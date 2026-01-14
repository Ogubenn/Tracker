<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Kullanıcının bu isteği yapma yetkisi olup olmadığını belirle.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * İstek için geçerli olan validasyon kuralları.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ad' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'rol' => 'required|in:admin,personel',
        ];
    }

    /**
     * Validasyon hata mesajlarını özelleştir.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ad.required' => 'Ad alanı zorunludur.',
            'ad.string' => 'Ad alanı metin olmalıdır.',
            'ad.max' => 'Ad alanı en fazla 255 karakter olabilir.',
            'email.required' => 'E-posta adresi zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kullanılıyor.',
            'password.required' => 'Şifre zorunludur.',
            'password.string' => 'Şifre metin olmalıdır.',
            'password.min' => 'Şifre en az 6 karakter olmalıdır.',
            'password.confirmed' => 'Şifre onayı eşleşmiyor.',
            'rol.required' => 'Rol seçimi zorunludur.',
            'rol.in' => 'Rol admin veya personel olmalıdır.',
        ];
    }

    /**
     * Validasyon için kullanılan özellik adlarını özelleştir.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'ad' => 'ad',
            'email' => 'e-posta',
            'password' => 'şifre',
            'rol' => 'rol',
        ];
    }
}
