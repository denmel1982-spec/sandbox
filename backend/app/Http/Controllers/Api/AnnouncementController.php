<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    /**
     * Получить список объявлений (публичный доступ)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Announcement::with(['user', 'component'])
            ->active();

        // Текстовый поиск по заголовку и тексту
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Фильтрация по типу (покупка/продажа)
        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        // Фильтрация по дате
        if ($dateFilter = $request->get('date_filter')) {
            $query->dateFilter($dateFilter);
        }

        // Произвольный диапазон дат
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('published_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('published_at', '<=', $dateTo);
        }

        // Сортировка
        $sortField = $request->get('sort', 'published_at');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['title', 'price', 'published_at', 'views_count'];
        
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Пагинация
        $perPage = min((int) $request->get('per_page', 10), 100);
        $announcements = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $announcements->items(),
            'pagination' => [
                'current_page' => $announcements->currentPage(),
                'last_page' => $announcements->lastPage(),
                'per_page' => $announcements->perPage(),
                'total' => $announcements->total(),
            ],
        ]);
    }

    /**
     * Получить детальную информацию об объявлении
     */
    public function show(Announcement $announcement): JsonResponse
    {
        $announcement->load(['user', 'component']);
        $announcement->incrementViews();

        return response()->json([
            'success' => true,
            'data' => $announcement,
        ]);
    }

    /**
     * Создать объявление (только для авторизованных пользователей)
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Announcement::class);

        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'type' => 'required|in:buy,sell',
            'component_id' => 'nullable|exists:components,id',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = Announcement::STATUS_ACTIVE;
        $validated['published_at'] = now();

        $announcement = Announcement::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Объявление успешно создано',
            'data' => $announcement,
        ], 201);
    }

    /**
     * Обновить объявление (только владелец)
     */
    public function update(Request $request, Announcement $announcement): JsonResponse
    {
        $this->authorize('update', $announcement);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:500',
            'description' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $announcement->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Объявление успешно обновлено',
            'data' => $announcement->fresh(),
        ]);
    }

    /**
     * Архивировать объявление (только владелец)
     */
    public function archive(Announcement $announcement): JsonResponse
    {
        $this->authorize('update', $announcement);

        $announcement->update(['status' => Announcement::STATUS_ARCHIVED]);

        return response()->json([
            'success' => true,
            'message' => 'Объявление архивировано',
        ]);
    }

    /**
     * Удалить объявление (только владелец)
     */
    public function destroy(Announcement $announcement): JsonResponse
    {
        $this->authorize('delete', $announcement);

        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Объявление удалено',
        ]);
    }
}
