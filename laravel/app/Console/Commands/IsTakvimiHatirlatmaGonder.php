<?php

namespace App\Console\Commands;

use App\Models\IsTakvimi;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class IsTakvimiHatirlatmaGonder extends Command
{
    protected $signature = 'is-takvimi:hatirlatma-gonder';
    protected $description = 'Bugünkü işleri kullanıcılara mail ile gönder';

    public function handle()
    {
        // Ayar kontrolü
        $aktif = DB::table('site_ayarlari')
            ->where('anahtar', 'is_takvimi_hatirlatma_aktif')
            ->value('deger');

        if ($aktif != '1') {
            $this->info('İş takvimi hatırlatma maili kapalı.');
            return 0;
        }

        $bugun = Carbon::today();
        
        // Bugünkü işleri getir
        $isler = IsTakvimi::where('tarih', $bugun->toDateString())
            ->where('durum', '!=', 'tamamlandi')
            ->with(['atananKullanici', 'atananKullanicilar'])
            ->get();

        if ($isler->isEmpty()) {
            $this->info('Bugün için iş yok.');
            return 0;
        }

        // Kullanıcılara göre grupla
        $kullaniciIsler = [];
        
        foreach ($isler as $is) {
            try {
                // Pivot tablodan kullanıcıları al
                if (method_exists($is, 'atananKullanicilar') && $is->atananKullanicilar->count() > 0) {
                    foreach ($is->atananKullanicilar as $kullanici) {
                        if ($kullanici->mail_alsin) {
                            $kullaniciIsler[$kullanici->id][] = $is;
                        }
                    }
                }
                // Fallback: Tek kullanıcı
                elseif ($is->atananKullanici && $is->atananKullanici->mail_alsin) {
                    $kullaniciIsler[$is->atananKullanici->id][] = $is;
                }
            } catch (\Exception $e) {
                \Log::error('İş takvimi hatırlatma hatası: ' . $e->getMessage());
            }
        }

        // Her kullanıcıya mail gönder
        $gonderilen = 0;
        foreach ($kullaniciIsler as $kullaniciId => $kullaniciIsleri) {
            $kullanici = User::find($kullaniciId);
            
            if (!$kullanici || !$kullanici->email) {
                continue;
            }

            try {
                Mail::send('emails.is-takvimi-hatirlatma', [
                    'kullanici' => $kullanici,
                    'isler' => $kullaniciIsleri,
                    'tarih' => $bugun->isoFormat('D MMMM YYYY')
                ], function($message) use ($kullanici) {
                    $message->to($kullanici->email, $kullanici->ad)
                        ->subject('Bugünkü İşleriniz - ' . Carbon::today()->isoFormat('D MMMM YYYY'));
                });

                $gonderilen++;
                $this->info("Mail gönderildi: {$kullanici->ad}");
            } catch (\Exception $e) {
                $this->error("Mail gönderilemedi ({$kullanici->ad}): " . $e->getMessage());
            }
        }

        $this->info("Toplam {$gonderilen} kullanıcıya hatırlatma maili gönderildi.");
        return 0;
    }
}
