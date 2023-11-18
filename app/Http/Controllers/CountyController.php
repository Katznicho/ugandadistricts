<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCountyRequest;
use App\Http\Requests\UpdateCountyRequest;
use App\Models\County;
use App\Traits\ApiRequestTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Doctrine\DBAL\Query\QueryException;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CountyController extends Controller
{
    use ApiRequestTrait;
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
        try {
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

            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.FAILED'), $request->userAgent());

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
        } catch (\Throwable $th) {
            //throw $th;
            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.FAILED'), $request->userAgent());
            return $this->handleException($th, 'An error occurred', 500);
        }
    }

    //
    public function getCountyByUUID(Request $request, string $uuid)
    {

        try {
            // Validate the UUID parameter
            $validator = Validator::make(['uuid' => $uuid], [
                'uuid' => 'required|uuid', // Assumes the UUID follows the standard format
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid UUID format'], 422);
            }
            $county = County::where('uuid', $uuid)->select("uuid", "countyName")->firstOrFail();

            return response()->json([
                'data' => $county,
                'version' => $this->VERSION
            ]);
        } catch (ModelNotFoundException $e) {
            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.FAILED'), $request->userAgent());
            return $this->handleException($e, 'county not found', 404);
        } catch (ValidationException $e) {
            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.FAILED'), $request->userAgent());
            return $this->handleException($e, 'Validation failed', 422);
        } catch (QueryException $e) {
            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.FAILED'), $request->userAgent());
            return $this->handleException($e, 'Query error', 500);
        } catch (Exception $e) {
            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.FAILED'), $request->userAgent());
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
        try {
            // Validate the UUID parameter
            $validator = Validator::make(['uuid' => $uuid], [
                'uuid' => 'required|uuid', // Assumes the UUID follows the standard format
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid UUID format'], 422);
            }

            $county = County::where('uuid', $uuid)->with(['subcounties:uuid,subcountyName,countyCode'])->firstOrFail();

            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.SUCCESS'), $request->userAgent());

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
            return response()->json(['message' => 'No subcounties found for the district', 'data' => []], 404);
        } catch (\Throwable $th) {
            //throw $th;
            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.FAILED'), $request->userAgent());
            return $this->handleException($th, 'An error occurred', 500);
        }
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
        try {
            // Validate the UUID parameter
            $validator = Validator::make(['uuid' => $uuid], [
                'uuid' => 'required|uuid', // Assumes the UUID follows the standard format
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid UUID format'], 422);
            }


            $county = County::where('uuid', $uuid)->with("parishes:uuid,parishName,countyCode")->firstOrFail();

            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.SUCCESS'), $request->userAgent());

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
            return response()->json(['message' => 'No parishes found for the district', 'data' => []], 404);
        } catch (\Throwable $th) {
            //throw $th;

            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.FAILED'), $request->userAgent());

            return $this->handleException($th, 'An error occurred', 500);
        }
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
        try {

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

            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.SUCCESS'), $request->userAgent());

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
            return response()->json(['message' => 'No villages found for the district', 'data' => []], 404);
        } catch (\Throwable $th) {
            //throw $th;

            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.FAILED'), $request->userAgent());

            return $this->handleException($th, 'An error occurred', 500);
        }
    }
    //
}
