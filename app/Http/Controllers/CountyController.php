<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCountyRequest;
use App\Http\Requests\UpdateCountyRequest;
use App\Models\County;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Doctrine\DBAL\Query\QueryException;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CountyController extends Controller
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
    public function store(StoreCountyRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(County $county)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(County $county)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCountyRequest $request, County $county)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(County $county)
    {
        //
    }

    public function getCounties(Request $request)
    {
        // Set the default limit and version
        $limit = $request->input('limit', 100);
        $version = 1;

        // Get the requested page from the query parameters
        $page = max(1, $request->input('page', 1));

        // Get the sorting parameters from the query parameters
        $sortColumn = $request->input('sort_column', 'countyName');
        $sortOrder = $request->input('sort_order', 'asc');

        // Ensure sort order is valid
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'asc';

        // Include related entities (counties, subcounties, parishes, villages) based on user request
        $withEntities = collect(['subcounties', 'parishes', 'villages'])
            ->filter(fn ($entity) => $request->has("with_$entity"))
            ->toArray();

        // Create a query builder for districts with selected related entities
        $query = County::select('uuid', 'countyName')->with($withEntities);

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

    //
    public function getCountyByUUID(Request $request, string $uuid)
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
            $county = County::where('uuid', $uuid)->select("uuid", "countyName")->firstOrFail();

            return response()->json([
                'data' => $county,
                'version' => $this->VERSION
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->handleException($e, 'county not found', 404);
        } catch (ValidationException $e) {
            return $this->handleException($e, 'Validation failed', 422);
        } catch (QueryException $e) {
            return $this->handleException($e, 'Query error', 500);
        } catch (Exception $e) {
            return $this->handleException($e, 'An error occurred', 500);
        }
    }

    /**
     * Handle exceptions and return a consistent JSON structure.
     *
     * @param \Exception $e
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleException(Exception $e, string $message, int $statusCode)
    {
        // Report the exception for further analysis (logging, monitoring, etc.)
        report($e);

        return response()->json(['error' => $message], $statusCode);
    }




    /**
     * Get subcounties for a specific county
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountySubCounties(Request $request, string $uuid)
    {
        // Validate the UUID parameter
        $validator = Validator::make(['uuid' => $uuid], [
            'uuid' => 'required|uuid', // Assumes the UUID follows the standard format
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid UUID format'], 422);
        }

        $county = County::where('uuid', $uuid)->with(['subcounties:uuid,subcountyName,countyCode'])->firstOrFail();

        // Check if subcounties exist for the county
        if ($county->subcounties) {
            $county->subcounties->makeHidden('countyCode');

            return response()->json([
                'data' => [
                    'county' => [
                        'uuid' => $county->uuid,
                        'countyName' => $county->countyName,
                    ],
                    'subcounties' => $county->subcounties,
                ],
                'version' => $this->VERSION
            ]);
        }

        // Handle case when no subcounties are found
        return response()->json(['error' => 'No subcounties found for the district'], 404);
    }

    /**
     * Get parishes for a specific county.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountyParishes(Request $request, string $uuid)
    {
        // Validate the UUID parameter
        $validator = Validator::make(['uuid' => $uuid], [
            'uuid' => 'required|uuid', // Assumes the UUID follows the standard format
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid UUID format'], 422);
        }


        $county = County::where('uuid', $uuid)->with("parishes:uuid,parishName,countyCode")->firstOrFail();

        // Check if parishes exist for the county
        if ($county->parishes) {
            $county->parishes->makeHidden('countyCode');
            return response()->json([
                'data' => [
                    'county' => [
                        'uuid' => $county->uuid,
                        'countyName' => $county->countyName,
                    ],
                    'parishes' => $county->parishes,
                ],
                'version' => $this->VERSION
            ]);
        }

        // Handle case when no parishes are found
        return response()->json(['error' => 'No parishes found for the district'], 404);
    }

    /**
     * Get villages for a specific county.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountyVillages(Request $request, string $uuid)
    {
        // Validate the UUID parameter
        $validator = Validator::make(['uuid' => $uuid], [
            'uuid' => 'required|uuid', // Assumes the UUID follows the standard format
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid UUID format'], 422);
        }

        // Fetch district with villages
        $county = County::where('uuid', $uuid)->with("villages:uuid,countyCode,villageName")->firstOrFail();

        // Check if villages exist for the county
        if ($county->villages) {
            $county->villages->makeHidden('countyCode');
            return response()->json([
                'data' => [
                    'county' => [
                        'uuid' => $county->uuid,
                        'countyName' => $county->countyName,
                    ],
                    'villages' => $county->villages,
                ],
                'version' => $this->VERSION
            ]);
        }

        // Handle case when no villages are found
        return response()->json(['error' => 'No villages found for the district'], 404);
    }
    //
}
