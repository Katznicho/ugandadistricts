<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParishRequest;
use App\Http\Requests\UpdateParishRequest;
use App\Models\Parish;
use Illuminate\Http\Request;

class ParishController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParishRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Parish $parish)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Parish $parish)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParishRequest $request, Parish $parish)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Parish $parish)
    {
        //
    }

    public function getParishes(Request $request)
    {
        // Set the default limit and version
        $limit = $request->input('limit', 100);
        $version = 1;

        // Get the requested page from the query parameters
        $page = max(1, $request->input('page', 1));

        // Get the sorting parameters from the query parameters
        $sortColumn = $request->input('sort_column', 'parishName');
        $sortOrder = $request->input('sort_order', 'asc');

        // Ensure sort order is valid
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'asc';

        // Include related entities (counties, subcounties, parishes, villages) based on user request
        $withEntities = collect(['villages'])
            ->filter(fn ($entity) => $request->has("with_$entity"))
            ->toArray();

        // Create a query builder for districts with selected related entities
        $query = Parish::select('uuid', 'parishName')->with($withEntities);

        // Apply sorting based on the provided parameters
        $query->orderBy($sortColumn, $sortOrder);

        // Fetch paginated districts based on the query
        $districts = $query->paginate($limit, ['*'], 'page', $page);

        // Create a custom response structure
        $response = [
            'data' => $districts->items(),
            'pagination' => [
                'current_page' => $districts->currentPage(),
                'per_page' => $limit,
                'total' => $districts->total(),
            ],
            'version' => $version,
        ];

        return $response;
    }
}
