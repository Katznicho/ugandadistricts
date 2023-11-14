<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVillageRequest;
use App\Http\Requests\UpdateVillageRequest;
use App\Models\Village;
use Illuminate\Http\Request;
use Doctrine\DBAL\Query\QueryException;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class VillageController extends Controller
{
    private  string $VERSION = '1.0.0';
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
    public function store(StoreVillageRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Village $village)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Village $village)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVillageRequest $request, Village $village)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Village $village)
    {
        //
    }

    public function getVillages(Request  $request)
    {
        // Set the default limit and version
        $limit = $request->input('limit', 100);
        $version = 1;

        // Get the requested page from the query parameters
        $page = max(1, $request->input('page', 1));

        // Get the sorting parameters from the query parameters
        $sortColumn = $request->input('sort_column', 'villageName');
        $sortOrder = $request->input('sort_order', 'asc');

        // Ensure sort order is valid
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'asc';

        // Include related entities (counties, subcounties, parishes, villages) based on user request
        $withEntities = collect([])
            ->filter(fn ($entity) => $request->has("with_$entity"))
            ->toArray();

        // Create a query builder for districts with selected related entities
        $query = Village::select('uuid', 'villageName')->with($withEntities);

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


    public function getVillageByUUID(Request $request, string $uuid)
    {
        // Validate the UUID parameter
        $validator = Validator::make(['uuid' => $uuid], [
            'uuid' => 'required|uuid', // Assumes the UUID follows the standard format
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid UUID format'], 422);
        }

        try {
            $village = Village::where('uuid', $uuid)->select("uuid", "villageName")->firstOrFail();

            return response()->json([
                'data' => $village,
                'version' => $this->VERSION
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->handleException($e, 'parish not found', 404);
        } catch (ValidationException $e) {
            return $this->handleException($e, 'Validation failed', 422);
        } catch (QueryException $e) {
            return $this->handleException($e, 'Query error', 500);
        } catch (Exception $e) {
            return $this->handleException($e, 'An error occurred', 500);
        }
    }
}
