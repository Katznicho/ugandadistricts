<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDistrictRequest;
use App\Http\Requests\UpdateDistrictRequest;
use App\Models\District;
use Doctrine\DBAL\Query\QueryException;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DistrictController extends Controller
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
    public function store(StoreDistrictRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(District $district)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(District $district)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDistrictRequest $request, District $district)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(District $district)
    {
        //
    }

    /**
     * Get districts with pagination and optional sorting.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDistricts(Request $request)
    {
        // Set the default limit and version
        $limit = $request->input('limit', 100);
        //$version = 1;

        // Get the requested page from the query parameters
        $page = max(1, $request->input('page', 1));

        // Get the sorting parameters from the query parameters
        $sortColumn = $request->input('sort_column', 'districtName');
        $sortOrder = $request->input('sort_order', 'asc');

        // Ensure sort order is valid
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'asc';

        // Include related entities (counties, subcounties, parishes, villages) based on user request
        $withEntities = collect(['counties', 'subcounties', 'parishes', 'villages'])
            ->filter(fn ($entity) => $request->has("with_$entity"))
            ->toArray();

        // Create a query builder for districts with selected related entities
        $query = District::select('uuid', 'districtName')->with($withEntities);

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
            'version' => $this->VERSION,
        ];

        return $response;
    }

    /**
     * Get a district by UUID.
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Illuminate\Database\QueryException
     * @throws \Exception
     */
    /**
     * Get a district by UUID.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Illuminate\Database\QueryException
     * @throws \Exception
     */
    public function getDistrictByUUID(Request $request, string $uuid)
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
            // Proceed to fetch the district
            //$district = District::findOrFail($uuid);
            $district = District::where('uuid', $uuid)->select("uuid", "districtName")->firstOrFail();

            return response()->json([
                'data' => $district,
                'version' => $this->VERSION
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->handleException($e, 'District not found', 404);
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
     * Get counties for a specific district.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDistrictCounties(Request $request, string $uuid)
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
            // Retrieve district details with counties
            $district = District::where('uuid', $uuid)->with("counties:uuid,countyName,districtCode")->firstOrFail();

            // Extract relevant data
            $districtData = [
                'uuid' => $district->uuid,
                'districtName' => $district->districtName,
            ];



            $countiesData = $district->counties->makeHidden(['districtCode']);
            // Return response with district and counties data
            return response()->json([
                'district' => $districtData,
                'counties' => $countiesData,
                'version' => $this->VERSION
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->handleException($e, 'District not found', 404);
        } catch (ValidationException $e) {
            return $this->handleException($e, 'Validation failed', 422);
        } catch (QueryException $e) {
            return $this->handleException($e, 'Query error', 500);
        } catch (Exception $e) {
            return $this->handleException($e, 'An error occurred', 500);
        }
    }


    /**
     * Get subcounties for a specific district.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDistrictSubCounties(Request $request, string $uuid)
    {
        // Validate the UUID parameter
        $validator = Validator::make(['uuid' => $uuid], [
            'uuid' => 'required|uuid', // Assumes the UUID follows the standard format
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid UUID format'], 422);
        }

        // Fetch district with subcounties
        $district = District::where('uuid', $uuid)->with(['subcounties:uuid,subcountyName,districtCode'])->firstOrFail();

        // Check if subcounties exist for the district
        if ($district->subcounties) {
            $district->subcounties->makeHidden('districtCode');

            return response()->json([
                'data' => [
                    'district' => [
                        'uuid' => $district->uuid,
                        'districtName' => $district->districtName,
                    ],
                    'subcounties' => $district->subcounties,
                ],
                'version' => $this->VERSION
            ]);
        }

        // Handle case when no subcounties are found
        return response()->json(['error' => 'No subcounties found for the district'], 404);
    }

    /**
     * Get parishes for a specific district.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDistrictParishes(Request $request, string $uuid)
    {
        // Validate the UUID parameter
        $validator = Validator::make(['uuid' => $uuid], [
            'uuid' => 'required|uuid', // Assumes the UUID follows the standard format
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid UUID format'], 422);
        }

        // Fetch district with parishes
        $district = District::where('uuid', $uuid)->with("parishes:uuid,parishName,districtCode")->firstOrFail();

        // Check if parishes exist for the district
        if ($district->parishes) {
            $district->parishes->makeHidden('districtCode');
            return response()->json([
                'data' => [
                    'district' => [
                        'uuid' => $district->uuid,
                        'districtName' => $district->districtName,
                    ],
                    'parishes' => $district->parishes,
                ],
                'version' => $this->VERSION
            ]);
        }

        // Handle case when no parishes are found
        return response()->json(['error' => 'No parishes found for the district'], 404);
    }

    /**
     * Get villages for a specific district.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDistrictVillages(Request $request, string $uuid)
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
        $district = District::where('uuid', $uuid)->with("villages:uuid,districtCode,villageName")->firstOrFail();

        // Check if villages exist for the district
        if ($district->villages) {
            $district->villages->makeHidden('districtCode');
            return response()->json([
                'data' => [
                    'district' => [
                        'uuid' => $district->uuid,
                        'districtName' => $district->districtName,
                    ],
                    'villages' => $district->villages,
                ],
                'version' => $this->VERSION
            ]);
        }

        // Handle case when no villages are found
        return response()->json(['error' => 'No villages found for the district'], 404);
    }
}
