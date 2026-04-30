<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Component;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ComponentController extends Controller
{
    /**
     * Получить список компонентов с пагинацией, поиском и сортировкой
     */
    public function index(Request $request): JsonResponse
    {
        $query = Component::with(['category', 'supplier'])
            ->active();

        // Поиск по наименованию
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Фильтрация по категории
        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Фильтрация по цене
        if ($minPrice = $request->get('min_price')) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice = $request->get('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        // Фильтрация по году выпуска
        if ($yearFrom = $request->get('year_from')) {
            $query->where('year_of_production', '>=', $yearFrom);
        }
        if ($yearTo = $request->get('year_to')) {
            $query->where('year_of_production', '<=', $yearTo);
        }

        // Сортировка
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $allowedSorts = ['name', 'price', 'year_of_production', 'stock_quantity', 'created_at'];
        
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Пагинация
        $perPage = min((int) $request->get('per_page', 10), 100);
        $perPage = in_array($perPage, [3, 5, 10, 20, 50, 100]) ? $perPage : 10;

        $components = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $components->items(),
            'pagination' => [
                'current_page' => $components->currentPage(),
                'last_page' => $components->lastPage(),
                'per_page' => $components->perPage(),
                'total' => $components->total(),
                'from' => $components->firstItem(),
                'to' => $components->lastItem(),
            ],
        ]);
    }

    /**
     * Получить детальную информацию о компоненте
     */
    public function show(Component $component): JsonResponse
    {
        $component->load(['category', 'supplier']);

        return response()->json([
            'success' => true,
            'data' => $component,
        ]);
    }
}
