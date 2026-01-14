<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBinaRequest extends FormRequest
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
            'bina_adi' => 'required|string|max:255',
            'aktif_mi' => 'boolean',
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
            'bina_adi.required' => 'Bina adı zorunludur.',
            'bina_adi.string' => 'Bina adı metin olmalıdır.',
            'bina_adi.max' => 'Bina adı en fazla 255 karakter olabilir.',
            'aktif_mi.boolean' => 'Aktif durumu geçerli bir boolean değer olmalıdır.',
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
            'bina_adi' => 'bina adı',
            'aktif_mi' => 'aktif durumu',
        ];
    }
}
