<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bina;
use App\Models\Alan;
use App\Models\KontrolMaddesi;
use App\Models\KontrolKaydi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class SystemTestController extends Controller
{
    private $correctPassword = '2809';

    public function index(Request $request)
    {
        // Şifre kontrolü - Cookie tabanlı
        if (!$request->cookie('system_test_auth')) {
            return view('admin.system-test.login');
        }

        $tests = [
            'php' => $this->testPhp(),
            'database' => $this->testDatabase(),
            'models' => $this->testModels(),
            'storage' => $this->testStorage(),
            'pdf' => $this->testPdf(),
            'mail' => $this->testMail(),
            'cache' => $this->testCache(),
        ];

        return view('admin.system-test.index', compact('tests'));
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'password' => 'required'
        ]);

        if ($request->password === $this->correctPassword) {
            // Cookie ile auth sakla (30 gün)
            return redirect()->route('admin.system-test.index')
                ->withCookie(cookie('system_test_auth', 'true', 43200)) // 30 gün
                ->with('success', 'Giriş başarılı!');
        }

        return back()->with('error', 'Hatalı şifre!');
    }

    public function logout()
    {
        return redirect()->route('admin.system-test.index')
            ->withCookie(cookie()->forget('system_test_auth'))
            ->with('success', 'Çıkış yapıldı.');
    }

    public function clearCache(Request $request)
    {
        try {
            $results = [];
            
            // Cache temizle
            Artisan::call('cache:clear');
            $results[] = 'Cache temizlendi';
            
            // Config cache temizle
            Artisan::call('config:clear');
            $results[] = 'Config cache temizlendi';
            
            // Route cache temizle
            Artisan::call('route:clear');
            $results[] = 'Route cache temizlendi';
            
            // View cache temizle
            Artisan::call('view:clear');
            $results[] = 'View cache temizlendi';
            
            return response()->json([
                'success' => true,
                'message' => 'Tüm cache başarıyla temizlendi',
                'details' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cache temizlenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    private function testPhp()
    {
        return [
            'status' => 'success',
            'version' => PHP_VERSION,
            'extensions' => [
                'PDO' => extension_loaded('pdo'),
                'PDO MySQL' => extension_loaded('pdo_mysql'),
                'mbstring' => extension_loaded('mbstring'),
                'OpenSSL' => extension_loaded('openssl'),
                'JSON' => extension_loaded('json'),
                'cURL' => extension_loaded('curl'),
                'GD' => extension_loaded('gd'),
                'Fileinfo' => extension_loaded('fileinfo'),
                'Tokenizer' => extension_loaded('tokenizer'),
                'XML' => extension_loaded('xml'),
            ],
            'ini' => [
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
            ]
        ];
    }

    private function testDatabase()
    {
        try {
            DB::connection()->getPdo();
            
            return [
                'status' => 'success',
                'connection' => 'Bağlantı başarılı',
                'driver' => DB::connection()->getDriverName(),
                'database' => DB::connection()->getDatabaseName(),
                'tables' => DB::select('SHOW TABLES'),
                'stats' => [
                    'users' => User::count(),
                    'binalar' => Bina::count(),
                    'alanlar' => Alan::count(),
                    'kontrol_maddeleri' => KontrolMaddesi::count(),
                    'kontrol_kayitlari' => KontrolKaydi::count(),
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    private function testModels()
    {
        $results = [];
        
        $models = [
            'User' => User::class,
            'Bina' => Bina::class,
            'Alan' => Alan::class,
            'KontrolMaddesi' => KontrolMaddesi::class,
            'KontrolKaydi' => KontrolKaydi::class,
        ];

        foreach ($models as $name => $class) {
            try {
                $count = $class::count();
                $latest = $class::latest()->first();
                
                $results[$name] = [
                    'status' => 'success',
                    'count' => $count,
                    'latest_id' => $latest ? $latest->id : null,
                    'latest_created' => $latest ? $latest->created_at?->format('d.m.Y H:i') : null,
                ];
            } catch (\Exception $e) {
                $results[$name] = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    private function testStorage()
    {
        $directories = [
            'app' => storage_path('app'),
            'app/public' => storage_path('app/public'),
            'framework/cache' => storage_path('framework/cache'),
            'framework/sessions' => storage_path('framework/sessions'),
            'framework/views' => storage_path('framework/views'),
            'logs' => storage_path('logs'),
        ];

        $results = [];
        foreach ($directories as $name => $path) {
            $results[] = [
                'directory' => $name,
                'exists' => is_dir($path),
                'writable' => is_writable($path),
                'path' => $path,
            ];
        }

        return $results;
    }

    private function testPdf()
    {
        try {
            $pdf = Pdf::loadHTML('<h1>Test PDF</h1><p>Bu bir test PDF dosyasıdır.</p>');
            
            return [
                'status' => 'success',
                'message' => 'DomPDF çalışıyor',
                'class' => get_class($pdf),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    private function testMail()
    {
        try {
            $config = [
                'MAIL_MAILER' => config('mail.default'),
                'MAIL_HOST' => config('mail.mailers.smtp.host'),
                'MAIL_PORT' => config('mail.mailers.smtp.port'),
                'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
                'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
                'MAIL_FROM_ADDRESS' => config('mail.from.address'),
                'MAIL_FROM_NAME' => config('mail.from.name'),
            ];

            return [
                'status' => 'success',
                'config' => $config,
                'configured' => !empty($config['MAIL_HOST']) && !empty($config['MAIL_USERNAME']),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    private function testCache()
    {
        try {
            $driver = config('cache.default');
            $stores = config('cache.stores');
            
            return [
                'status' => 'success',
                'driver' => $driver,
                'config' => $stores[$driver] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
