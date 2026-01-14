<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Collection;

/**
 * PDF oluşturma ve yönetim servisi
 * 
 * DomPDF kütüphanesini kullanarak raporları PDF formatına çevirir.
 * Controller katmanını DomPDF detaylarından soyutlar.
 */
class PdfService
{
    private Dompdf $dompdf;
    
    /**
     * Service başlatılırken DomPDF yapılandırması
     */
    public function __construct()
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('fontDir', storage_path('fonts'));
        $options->set('fontCache', storage_path('fonts'));
        $options->set('tempDir', sys_get_temp_dir());
        $options->set('chroot', base_path());
        $options->set('defaultFont', 'DejaVu Sans');
        
        $this->dompdf = new Dompdf($options);
    }
    
    /**
     * Kontrol raporu PDF'i oluşturur
     * 
     * @param Collection $kayitlar Kontrol kayıtları (gruplu)
     * @param string $tarihAralik Tarih aralığı metni (örn: "01.01.2026 - 15.01.2026")
     * @param string $secilenBina Seçilen bina adı veya "Tüm Binalar"
     * @return string PDF binary içeriği
     * @throws \Exception DomPDF hataları
     */
    public function generateRaporPdf(Collection $kayitlar, string $tarihAralik, string $secilenBina): string
    {
        // View'i HTML'e çevir
        $html = view('admin.raporlar.pdf', [
            'kayitlar' => $kayitlar,
            'tarihAralik' => $tarihAralik,
            'secilenBina' => $secilenBina,
        ])->render();
        
        // PDF oluştur
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('a4', 'portrait');
        $this->dompdf->render();
        
        return $this->dompdf->output();
    }
    
    /**
     * Genel amaçlı HTML'den PDF oluşturma
     * 
     * @param string $html HTML içeriği
     * @param string $paper Kağıt boyutu (varsayılan: 'a4')
     * @param string $orientation Yönelim (varsayılan: 'portrait')
     * @return string PDF binary içeriği
     */
    public function generateFromHtml(string $html, string $paper = 'a4', string $orientation = 'portrait'): string
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper($paper, $orientation);
        $this->dompdf->render();
        
        return $this->dompdf->output();
    }
    
    /**
     * View'den direkt PDF oluşturma
     * 
     * @param string $view View adı
     * @param array $data View'e gönderilecek data
     * @param string $paper Kağıt boyutu
     * @param string $orientation Yönelim
     * @return string PDF binary içeriği
     */
    public function generateFromView(string $view, array $data = [], string $paper = 'a4', string $orientation = 'portrait'): string
    {
        $html = view($view, $data)->render();
        return $this->generateFromHtml($html, $paper, $orientation);
    }
}
