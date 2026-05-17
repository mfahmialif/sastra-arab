<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ActivityLogRead;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = ActivityLog::query()
            ->with('user:id,name,email')
            ->withExists(['reads as read_by_current_user' => fn ($readQuery) => $readQuery->where('user_id', $user->id)])
            ->latest();

        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('subject_title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($request->integer('per_page', 15));
    }

    public function notifications(Request $request)
    {
        $user = $request->user();
        $unreadQuery = ActivityLog::query()->unreadFor($user);

        return response()->json([
            'unread_count' => (clone $unreadQuery)->count(),
            'items' => (clone $unreadQuery)
                ->with('user:id,name,email')
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }

    public function markRead(Request $request)
    {
        $user = $request->user();
        $ids = $request->input('ids');

        $query = ActivityLog::query()->unreadFor($user);
        if (is_array($ids) && count($ids)) {
            $query->whereIn('id', $ids);
        }

        $now = now();
        $rows = $query->pluck('id')->map(fn ($id) => [
            'activity_log_id' => $id,
            'user_id' => $user->id,
            'read_at' => $now,
        ])->all();

        if ($rows) {
            ActivityLogRead::query()->insertOrIgnore($rows);
        }

        return $this->notifications($request);
    }
}
