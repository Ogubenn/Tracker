<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->latest();

        // Filtreleme
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model')) {
            $query->where('model', $request->model);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        // Filtre için kullanıcılar
        $users = \App\Models\User::where('aktif_mi', true)
            ->orderBy('ad')
            ->get();

        // Model listesi
        $models = ActivityLog::select('model')
            ->distinct()
            ->pluck('model');

        return view('admin.activity_logs.index', compact('logs', 'users', 'models'));
    }

    public function show(ActivityLog $log): View
    {
        $log->load('user');
        return view('admin.activity_logs.show', compact('log'));
    }

    public function clear(Request $request)
    {
        $validated = $request->validate([
            'clear_type' => 'required|in:older_than,date_range,all',
            'days' => 'required_if:clear_type,older_than|nullable|integer|min:1|max:365',
            'date_from' => 'required_if:clear_type,date_range|nullable|date',
            'date_to' => 'required_if:clear_type,date_range|nullable|date|after_or_equal:date_from',
        ]);

        $query = ActivityLog::query();
        $message = '';

        switch ($validated['clear_type']) {
            case 'older_than':
                $date = now()->subDays($validated['days']);
                $query->where('created_at', '<', $date);
                $message = $validated['days'] . ' günden eski ';
                break;

            case 'date_range':
                $query->whereDate('created_at', '>=', $validated['date_from'])
                      ->whereDate('created_at', '<=', $validated['date_to']);
                $message = $validated['date_from'] . ' - ' . $validated['date_to'] . ' tarihleri arasındaki ';
                break;

            case 'all':
                // Tüm kayıtlar silinecek
                $message = 'Tüm ';
                break;
        }

        $count = $query->delete();

        return redirect()->route('admin.activity-logs.index')
            ->with('success', '✅ ' . $message . $count . ' adet log kaydı başarıyla silindi.');
    }
}
