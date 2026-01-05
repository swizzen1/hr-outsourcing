<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\VacancyPublicResource;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicVacancyController
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $paginator = Vacancy::publicVisible()
            ->orderByDesc('published_at')
            ->paginate($perPage);

        $data = $paginator->getCollection()->map(function (Vacancy $vacancy) use ($request) {
            return (new VacancyPublicResource($vacancy))->toArray($request);
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResource
    {
        $vacancy = Vacancy::publicVisible()->findOrFail($id);

        return new VacancyPublicResource($vacancy);
    }
}
