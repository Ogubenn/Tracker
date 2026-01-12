<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bina;
use App\Models\KontrolMaddesi;
use App\Models\KontrolKaydi;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $binaSayisi = Bina::where('aktif_mi', true)->count();
        $kontrolMaddesiSayisi = KontrolMaddesi::where('aktif_mi', true)->count();
        $personelSayisi = User::where('rol', 'personel')->where('aktif_mi', true)->count();
        
        $bugunYapilanKontroller = KontrolKaydi::whereDate('tarih', Carbon::today())->count();

        return view('admin.dashboard', compact(
            'binaSayisi',
            'kontrolMaddesiSayisi',
            'personelSayisi',
            'bugunYapilanKontroller'
        ));
    }
}
