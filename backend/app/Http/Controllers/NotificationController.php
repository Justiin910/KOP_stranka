<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected TranslationService $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * Get all notifications for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $userLanguage = $user->language ?? 'sk'; // Default to Slovak

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) use ($userLanguage) {
                $data = [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'read' => $notification->read,
                    'time' => $this->getFormattedTime($notification->created_at),
                    'created_at' => $notification->created_at,
                ];

                // Translate if user's language differs from stored language (Slovak)
                if ($userLanguage !== 'sk') {
                    $data = $this->translationService->translateNotification($data, $userLanguage);
                }

                return $data;
            });

        return response()->json([
            'data' => $notifications,
            'unread_count' => $notifications->where('read', false)->count(),
        ]);
    }

    /**
     * Create a new notification (Admin only)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'nullable|exists:users,id',
                'type' => 'required|in:order,payment,promotion,system,general',
                'title' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            $notification = Notification::create($validated);

            return response()->json([
                'message' => __('messages.notification.created'),
                'data' => $notification,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => __('messages.notification.validation_error'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('messages.notification.creation_failed', ['error' => $e->getMessage()]),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id): JsonResponse
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json(['message' => __('messages.notification.not_found')], 404);
        }

        $notification->update(['read' => true]);

        return response()->json(['message' => __('messages.notification.marked_as_read')]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        Notification::where('user_id', $request->user()->id)
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['message' => __('messages.notification.all_marked_as_read')]);
    }

    /**
     * Delete a notification
     */
    public function destroy($id): JsonResponse
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json(['message' => __('messages.notification.not_found')], 404);
        }

        $notification->delete();

        return response()->json(['message' => __('messages.notification.deleted')]);
    }

    /**
     * Get all notifications (Admin only)
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userLanguage = $user->language ?? 'sk'; // Default to Slovak

            $notifications = Notification::orderBy('created_at', 'desc')
                ->get()
                ->map(function ($notification) use ($userLanguage) {
                    $data = [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'read' => $notification->read,
                        'time' => $this->getFormattedTime($notification->created_at),
                        'created_at' => $notification->created_at,
                    ];

                    // Translate if user's language differs from stored language (Slovak)
                    if ($userLanguage !== 'sk') {
                        $data = $this->translationService->translateNotification($data, $userLanguage);
                    }

                    return $data;
                });

            return response()->json([
                'data' => $notifications,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('messages.notification.loading_failed', ['error' => $e->getMessage()]),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Broadcast notification to all users
     */
    public function broadcast(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:order,payment,promotion,system,general',
                'title' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            // Create notification for all users
            $users = \App\Models\User::pluck('id');

            if ($users->isEmpty()) {
                return response()->json([
                    'message' => __('messages.notification.no_users'),
                    'users_count' => 0,
                ], 200);
            }

            foreach ($users as $userId) {
                Notification::create([
                    'user_id' => $userId,
                    'type' => $validated['type'],
                    'title' => $validated['title'],
                    'message' => $validated['message'],
                ]);
            }

            return response()->json([
                'message' => __('messages.notification.broadcast_sent'),
                'users_count' => $users->count(),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => __('messages.notification.validation_error'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('messages.notification.broadcast_failed', ['error' => $e->getMessage()]),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format time for frontend display
     */
    private function getFormattedTime($createdAt): string
    {
        $now = now();
        $diff = $now->diffInMinutes($createdAt);

        if ($diff < 1) {
            return 'práve teraz';
        } elseif ($diff < 60) {
            return "Pred $diff min";
        } elseif ($diff < 1440) {
            $hours = $now->diffInHours($createdAt);
            return "Pred $hours h";
        } else {
            $days = $now->diffInDays($createdAt);
            if ($days < 7) {
                return "Pred $days d";
            }
            return $createdAt->format('d.m.Y');
        }
    }
}
