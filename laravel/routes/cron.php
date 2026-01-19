<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Cron Trigger Route (External Cron Services için)
|--------------------------------------------------------------------------
| Bu route external cron servisleri (cron-job.org gibi) tarafından
| tetiklenebilir. Güvenlik için secret key kontrolü yapar.
|
| Kullanım: https://siteniz.com/cron-trigger?key=CRON_SECRET_KEY
*/

Route::get('/cron-trigger', function () {
    // Güvenlik kontrolü
    $secretKey = env('CRON_SECRET_KEY');
    
    if (!$secretKey || request('key') !== $secretKey) {
        abort(403, 'Unauthorized cron access');
    }
    
    try {
        // Schedule:run komutunu çalıştır
        Artisan::call('schedule:run');
        $output = Artisan::output();
        
        return response()->json([
            'success' => true,
            'message' => 'Scheduled tasks executed',
            'output' => $output,
            'timestamp' => now()->toDateTimeString()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
