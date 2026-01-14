<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKontrolMaddesiRequest extends FormRequest
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
            'bina_id' => 'required|exists:binalar,id',
            'kontrol_adi' => 'required|string|max:255',
            'kontrol_tipi' => 'required|in:checkbox,sayisal,metin',
            'birim' => 'nullable|string|max:20',
            'zaman_secimi' => 'boolean',
            'periyot' => 'required|in:gunluk,haftalik,15_gun,aylik',
            'haftalik_gun' => 'nullable|in:pazartesi,sali,carsamba,persembe,cuma,cumartesi,pazar',
            'aktif_mi' => 'boolean',
            'sira' => 'integer|min:0',
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
            'bina_id.required' => 'Bina seçimi zorunludur.',
            'bina_id.exists' => 'Seçilen bina geçerli değil.',
            'kontrol_adi.required' => 'Kontrol adı zorunludur.',
            'kontrol_adi.string' => 'Kontrol adı metin olmalıdır.',
            'kontrol_adi.max' => 'Kontrol adı en fazla 255 karakter olabilir.',
            'kontrol_tipi.required' => 'Kontrol tipi zorunludur.',
            'kontrol_tipi.in' => 'Kontrol tipi geçerli değil. checkbox, sayisal veya metin olmalıdır.',
            'periyot.required' => 'Periyot zorunludur.',
            'periyot.in' => 'Periyot geçerli değil. gunluk, haftalik, 15_gun veya aylik olmalıdır.',
            'haftalik_gun.in' => 'Haftalık gün geçerli değil.',
            'aktif_mi.boolean' => 'Aktif durumu geçerli bir boolean değer olmalıdır.',
            'sira.integer' => 'Sıra numarası tam sayı olmalıdır.',
            'sira.min' => 'Sıra numarası en az 0 olmalıdır.',
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
            'bina_id' => 'bina',
            'kontrol_adi' => 'kontrol adı',
            'kontrol_tipi' => 'kontrol tipi',
            'periyot' => 'periyot',
            'haftalik_gun' => 'haftalık gün',
            'aktif_mi' => 'aktif durumu',
            'sira' => 'sıra numarası',
        ];
    }
}
