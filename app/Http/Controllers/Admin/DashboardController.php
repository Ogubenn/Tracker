<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bina;
use App\Models\KontrolMaddesi;
use App\Models\KontrolKaydi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'binaSayisi' => $this->getAktifBinaSayisi(),
            'kontrolMaddesiSayisi' => $this->getAktifKontrolMaddesiSayisi(),
            'personelSayisi' => $this->getAktifPersonelSayisi(),
            'bugunYapilanKontroller' => $this->getBugunYapilanKontrolSayisi(),
        ]);
    }

    private function getAktifBinaSayisi(): int
    {
        return Bina::where('aktif_mi', true)->count();
    }

    private function getAktifKontrolMaddesiSayisi(): int
    {
        return KontrolMaddesi::where('aktif_mi', true)->count();
    }

    private function getAktifPersonelSayisi(): int
    {
        return User::where('rol', 'personel')
            ->where('aktif_mi', true)
            ->count();
    }

    private function getBugunYapilanKontrolSayisi(): int
    {
        return KontrolKaydi::whereDate('tarih', Carbon::today())->count();
    }
}
