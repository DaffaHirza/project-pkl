<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $query = Notification::where('notifiable_type', get_class($request->user()))
            ->where('notifiable_id', $request->user()->id)
            ->orderBy('created_at', 'desc');

        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        $notifications = $query->paginate(20);
        $unreadCount = Notification::where('notifiable_type', get_class($request->user()))
            ->where('notifiable_id', $request->user()->id)
            ->unread()
            ->count();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'notifications' => $notifications->items(),
                'unread_count' => $unreadCount,
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ],
            ]);
        }

        $types = Notification::TYPES;

        return view('notifications.index', compact('notifications', 'unreadCount', 'types'));
    }

    /**
     * Get recent notifications (for dropdown/header)
     */
    public function recent(Request $request)
    {
        $notifications = Notification::where('notifiable_type', get_class($request->user()))
            ->where('notifiable_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = Notification::where('notifiable_type', get_class($request->user()))
            ->where('notifiable_id', $request->user()->id)
            ->unread()
            ->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications->map(fn($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'message' => $n->message,
                'icon' => $n->icon,
                'color' => $n->color,
                'action_url' => $n->action_url,
                'is_read' => $n->isRead(),
                'created_at' => $n->created_at->diffForHumans(),
            ]),
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Get unread count only (for badge updates)
     */
    public function unreadCount(Request $request)
    {
        $count = Notification::where('notifiable_type', get_class($request->user()))
            ->where('notifiable_id', $request->user()->id)
            ->unread()
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        // Verify ownership
        if ($notification->notifiable_id != $request->user()->id ||
            $notification->notifiable_type !== get_class($request->user())) {
            abort(403, 'Unauthorized');
        }

        $notification->markAsRead();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sudah dibaca.',
            ]);
        }

        // Redirect to action URL if exists
        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return back();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        Notification::where('notifiable_type', get_class($request->user()))
            ->where('notifiable_id', $request->user()->id)
            ->unread()
            ->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi ditandai sudah dibaca.',
            ]);
        }

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /**
     * Mark a notification as unread
     */
    public function markAsUnread(Request $request, Notification $notification)
    {
        // Verify ownership
        if ($notification->notifiable_id != $request->user()->id ||
            $notification->notifiable_type !== get_class($request->user())) {
            abort(403, 'Unauthorized');
        }

        $notification->markAsUnread();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai belum dibaca.',
            ]);
        }

        return back();
    }

    /**
     * Delete a specific notification
     */
    public function destroy(Request $request, Notification $notification)
    {
        // Verify ownership
        if ($notification->notifiable_id != $request->user()->id ||
            $notification->notifiable_type !== get_class($request->user())) {
            abort(403, 'Unauthorized');
        }

        $notification->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil dihapus.',
            ]);
        }

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Delete all read notifications
     */
    public function destroyAllRead(Request $request)
    {
        $count = Notification::where('notifiable_type', get_class($request->user()))
            ->where('notifiable_id', $request->user()->id)
            ->read()
            ->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $count . ' notifikasi berhasil dihapus.',
            ]);
        }

        return back()->with('success', $count . ' notifikasi berhasil dihapus.');
    }

    /**
     * Delete all notifications
     */
    public function destroyAll(Request $request)
    {
        $count = Notification::where('notifiable_type', get_class($request->user()))
            ->where('notifiable_id', $request->user()->id)
            ->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi berhasil dihapus.',
            ]);
        }

        return back()->with('success', 'Semua notifikasi berhasil dihapus.');
    }

    /**
     * Get notification preferences/settings
     */
    public function settings(Request $request)
    {
        // This could be expanded to store user preferences in a separate table
        $types = Notification::TYPES;
        
        return view('notifications.settings', compact('types'));
    }

    /**
     * Update notification preferences/settings
     */
    public function updateSettings(Request $request)
    {
        // Placeholder for notification preferences
        // Could store in user_notification_settings table
        
        return back()->with('success', 'Pengaturan notifikasi berhasil disimpan.');
    }
}
