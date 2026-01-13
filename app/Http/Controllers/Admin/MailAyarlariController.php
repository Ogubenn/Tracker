<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteAyarlari;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MailAyarlariController extends Controller
{
    public function index(): View
    {
        $ayarlar = [
            'eksik_kontrol_mail_aktif' => SiteAyarlari::getBool('eksik_kontrol_mail_aktif'),
            'eksik_kontrol_sabah_saat' => SiteAyarlari::get('eksik_kontrol_sabah_saat', '07:00'),
            'eksik_kontrol_aksam_saat' => SiteAyarlari::get('eksik_kontrol_aksam_saat', '19:00'),
            'toplu_rapor_mail_aktif' => SiteAyarlari::getBool('toplu_rapor_mail_aktif'),
            'toplu_rapor_saat' => SiteAyarlari::get('toplu_rapor_saat', '19:00'),
        ];

        return view('admin.mail-ayarlari.index', compact('ayarlar'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $this->validateSettings($request);

        SiteAyarlari::set('eksik_kontrol_mail_aktif', $validated['eksik_kontrol_mail_aktif'] ?? '0');
        SiteAyarlari::set('eksik_kontrol_sabah_saat', $validated['eksik_kontrol_sabah_saat']);
        SiteAyarlari::set('eksik_kontrol_aksam_saat', $validated['eksik_kontrol_aksam_saat']);
        SiteAyarlari::set('toplu_rapor_mail_aktif', $validated['toplu_rapor_mail_aktif'] ?? '0');
        SiteAyarlari::set('toplu_rapor_saat', $validated['toplu_rapor_saat']);

        return redirect()->route('admin.mail-ayarlari.index')
            ->with('success', 'Mail ayarları başarıyla güncellendi.');
    }

    private function validateSettings(Request $request): array
    {
        return $request->validate([
            'eksik_kontrol_mail_aktif' => 'nullable|in:1',
            'eksik_kontrol_sabah_saat' => 'required|date_format:H:i',
            'eksik_kontrol_aksam_saat' => 'required|date_format:H:i',
            'toplu_rapor_mail_aktif' => 'nullable|in:1',
            'toplu_rapor_saat' => 'required|date_format:H:i',
        ]);
    }
}
