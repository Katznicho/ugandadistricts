<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubCountyRequest;
use App\Http\Requests\UpdateSubCountyRequest;
use App\Models\SubCounty;
use Illuminate\Http\Request;
use Doctrine\DBAL\Query\QueryException;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class SubCountyController extends Controller
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
    public function store(StoreSubCountyRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SubCounty $subCounty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubCounty $subCounty)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubCountyRequest $request, SubCounty $subCounty)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubCounty $subCounty)
    {
        //
    }

    public function getSubCounties(Request $request)
    {
        // Set the default limit and version
        $limit = $request->input('limit', 100);
        $version = 1;

        // Get the requested page from the query parameters
        $page = max(1, $request->input('page', 1));

        // Get the sorting parameters from the query parameters
        $sortColumn = $request->input('sort_column', 'subCountyName');
        $sortOrder = $request->input('sort_order', 'asc');

        // Ensure sort order is valid
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'asc';

        // Include related entities (counties, subcounties, parishes, villages) based on user request
        $withEntities = collect(['parishes', 'villages'])
            ->filter(fn ($entity) => $request->has("with_$entity"))
            ->toArray();

        // Create a query builder for districts with selected related entities
        $query = SubCounty::select('uuid', 'subCountyName')->with($withEntities);

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
    public function getSubCountyByUUID(Request $request, string $uuid)
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
            $sub_county = SubCounty::where('uuid', $uuid)->select("uuid", "subCountyName")->firstOrFail();

            return response()->json([
                'data' => $sub_county,
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
     * Get parishes for a specific sub county county.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubCountyParishes(Request $request, string $uuid)
    {
        // Validate the UUID parameter
        $validator = Validator::make(['uuid' => $uuid], [
            'uuid' => 'required|uuid', // Assumes the UUID follows the standard format
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid UUID format'], 422);
        }


        $sub_county = SubCounty::where('uuid', $uuid)->with("parishes:uuid,parishName,subCountyCode")->firstOrFail();

        // Check if parishes exist for the county
        if ($sub_county->parishes) {
            $sub_county->parishes->makeHidden('subCountyCode');
            return response()->json([
                'data' => [
                    'sub$sub_county' => [
                        'uuid' => $sub_county->uuid,
                        //'sub$sub_countyName' => $sub_county->sub$sub_countyName,
                        'subCountyName' => $sub_county->subCountyName
                    ],
                    'parishes' => $sub_county->parishes,
                ],
                'version' => $this->VERSION
            ]);
        }

        // Handle case when no parishes are found
        return response()->json(['error' => 'No parishes found for the district'], 404);
    }

    /**
     * Get villages for a specific sub county.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubCountyVillages(Request $request, string $uuid)
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
        $sub_county = SubCounty::where('uuid', $uuid)->with("villages:uuid,subCountyCode,villageName")->firstOrFail();

        // Check if villages exist for the county
        if ($sub_county->villages) {
            $sub_county->villages->makeHidden('subCountyCode');
            return response()->json([
                'data' => [
                    'county' => [
                        'uuid' => $sub_county->uuid,
                        'countyName' => $sub_county->countyName,
                    ],
                    'villages' => $sub_county->villages,
                ],
                'version' => $this->VERSION
            ]);
        }

        // Handle case when no villages are found
        return response()->json(['error' => 'No villages found for the district'], 404);
    }
    //
}
