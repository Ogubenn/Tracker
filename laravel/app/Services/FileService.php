<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Fotoğrafları yükle ve optimize et
     * 
     * @param array $files
     * @param string $directory
     * @return array Yüklenen dosya yolları
     */
    public function uploadPhotos(array $files, string $directory = 'kontrol_fotograflari'): array
    {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $path = $this->uploadAndOptimizePhoto($file, $directory);
                if ($path) {
                    $paths[] = $path;
                }
            }
        }

        return $paths;
    }

    /**
     * Tek fotoğraf yükle ve optimize et
     * 
     * @param UploadedFile $file
     * @param string $directory
     * @return string|null
     */
    public function uploadAndOptimizePhoto(UploadedFile $file, string $directory = 'kontrol_fotograflari'): ?string
    {
        try {
            // Benzersiz dosya adı oluştur
            $filename = $this->generateUniqueFilename($file);
            $subDirectory = $directory . '/' . date('Y/m');
            $path = $subDirectory . '/' . $filename;

            // BASİT YÖNTEM - Doğrudan Laravel Storage kullan (optimizasyon yok)
            try {
                // Dosyayı olduğu gibi kaydet
                $content = file_get_contents($file->getRealPath());
                $saved = Storage::disk('public')->put($path, $content);
                
                if ($saved) {
                    \Log::info('Fotoğraf basit yöntemle yüklendi: ' . $path);
                    return $path;
                }
            } catch (\Exception $e) {
                \Log::error('Basit yöntem başarısız: ' . $e->getMessage());
            }

            // YEDEK YÖNTEM - GD ile optimize et
            $fullPath = Storage::disk('public')->path($subDirectory);
            if (!file_exists($fullPath)) {
                @mkdir($fullPath, 0777, true);
            }

            $tempPath = $file->getRealPath();
            $imageInfo = @getimagesize($tempPath);
            
            if (!$imageInfo) {
                // Resim değilse bile kaydet
                \Log::warning('Resim formatı tanımlanamadı, direkt kaydediliyor');
                $file->move(Storage::disk('public')->path($subDirectory), $filename);
                return $path;
            }

            $mime = $imageInfo['mime'];
            $source = null;
            
            switch ($mime) {
                case 'image/jpeg':
                    $source = @imagecreatefromjpeg($tempPath);
                    break;
                case 'image/png':
                    $source = @imagecreatefrompng($tempPath);
                    break;
                case 'image/gif':
                    $source = @imagecreatefromgif($tempPath);
                    break;
                case 'image/webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $source = @imagecreatefromwebp($tempPath);
                    }
                    break;
            }

            if (!$source) {
                // GD başarısız, dosyayı direkt taşı
                \Log::warning('GD ile açılamadı, direkt taşınıyor');
                $file->move(Storage::disk('public')->path($subDirectory), $filename);
                return $path;
            }

            // GD ile optimize et
            $source = $this->fixOrientation($source, $tempPath);
            $width = imagesx($source);
            $height = imagesy($source);

            if ($width > 1920) {
                $newWidth = 1920;
                $newHeight = (int)($height * ($newWidth / $width));
                $destination = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($source);
                $source = $destination;
            }

            $fullOutputPath = Storage::disk('public')->path($path);
            $saved = @imagejpeg($source, $fullOutputPath, 85);
            imagedestroy($source);

            if ($saved && file_exists($fullOutputPath)) {
                \Log::info('Fotoğraf optimize edilerek yüklendi: ' . $path);
                return $path;
            }
            
            \Log::error('Tüm yöntemler başarısız oldu');
            return null;

            return $path;
        } catch (\Exception $e) {
            \Log::error('Fotoğraf yükleme hatası: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * EXIF Orientation düzeltme
     */
    private function fixOrientation($image, $filepath)
    {
        if (!function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($filepath);
        if (!$exif || !isset($exif['Orientation'])) {
            return $image;
        }

        $orientation = $exif['Orientation'];
        
        switch ($orientation) {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;
            case 6:
                $image = imagerotate($image, -90, 0);
                break;
            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }

        return $image;
    }

    /**
     * Fotoğrafı sil
     * 
     * @param string $path
     * @return bool
     */
    public function deletePhoto(string $path): bool
    {
        try {
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->delete($path);
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Fotoğraf silme hatası: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Birden fazla fotoğrafı sil
     * 
     * @param array $paths
     * @return void
     */
    public function deletePhotos(array $paths): void
    {
        foreach ($paths as $path) {
            $this->deletePhoto($path);
        }
    }

    /**
     * Benzersiz dosya adı oluştur
     * 
     * @param UploadedFile $file
     * @return string
     */
    private function generateUniqueFilename(UploadedFile $file): string
    {
        return Str::random(40) . '_' . time() . '.jpg';
    }

    /**
     * Fotoğraf URL'ini al
     * 
     * @param string $path
     * @return string
     */
    public function getPhotoUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }

    /**
     * Thumbnail oluştur
     * 
     * @param string $path
     * @param int $width
     * @param int $height
     * @return string|null
     */
    public function createThumbnail(string $path, int $width = 300, int $height = 300): ?string
    {
        try {
            if (!Storage::disk('public')->exists($path)) {
                return null;
            }

            $thumbnailPath = 'thumbnails/' . $path;
            
            // Zaten varsa tekrar oluşturma
            if (Storage::disk('public')->exists($thumbnailPath)) {
                return $thumbnailPath;
            }

            // Orijinal resmi oku
            $fullPath = Storage::disk('public')->path($path);
            $imageInfo = getimagesize($fullPath);
            
            if (!$imageInfo) {
                return null;
            }

            $mime = $imageInfo['mime'];
            switch ($mime) {
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($fullPath);
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($fullPath);
                    break;
                case 'image/gif':
                    $source = imagecreatefromgif($fullPath);
                    break;
                case 'image/webp':
                    $source = imagecreatefromwebp($fullPath);
                    break;
                default:
                    return null;
            }

            if (!$source) {
                return null;
            }

            // Orijinal boyutlar
            $origWidth = imagesx($source);
            $origHeight = imagesy($source);

            // Aspect ratio hesapla
            $ratio = min($width / $origWidth, $height / $origHeight);
            $newWidth = (int)($origWidth * $ratio);
            $newHeight = (int)($origHeight * $ratio);

            // Thumbnail oluştur
            $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
            imagedestroy($source);

            // Temp dosyaya kaydet
            $tempOutput = sys_get_temp_dir() . '/' . basename($path);
            imagejpeg($thumbnail, $tempOutput, 80);
            imagedestroy($thumbnail);

            // Storage'a kaydet
            $content = file_get_contents($tempOutput);
            Storage::disk('public')->put($thumbnailPath, $content);
            unlink($tempOutput);

            return $thumbnailPath;
        } catch (\Exception $e) {
            \Log::error('Thumbnail oluşturma hatası: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Dosya boyutunu kontrol et (MB cinsinden)
     * 
     * @param UploadedFile $file
     * @param int $maxSizeMB
     * @return bool
     */
    public function checkFileSize(UploadedFile $file, int $maxSizeMB = 10): bool
    {
        $maxSizeBytes = $maxSizeMB * 1024 * 1024;
        return $file->getSize() <= $maxSizeBytes;
    }

    /**
     * İzin verilen dosya tiplerini kontrol et
     * 
     * @param UploadedFile $file
     * @return bool
     */
    public function isValidImageType(UploadedFile $file): bool
    {
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        return in_array($file->getMimeType(), $allowedMimes);
    }
}
