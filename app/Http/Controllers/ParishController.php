<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParishRequest;
use App\Http\Requests\UpdateParishRequest;
use App\Models\Parish;
use App\Traits\ApiRequestTrait;
use Illuminate\Http\Request;
use Doctrine\DBAL\Query\QueryException;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class ParishController extends Controller
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
        try {
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

            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.SUCCESS'), $request->userAgent());
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
    public function getParishByUUID(Request $request, string $uuid)
    {


        try {
            // Validate the UUID parameter
            $validator = Validator::make(['uuid' => $uuid], [
                'uuid' => 'required|uuid', // Assumes the UUID follows the standard format
            ]);

            // Check if validation fails
            if ($validator->fails()) {

                return response()->json(['message' => 'Invalid UUID format'], 422);
            }
            $parish = Parish::where('uuid', $uuid)->select("uuid", "parishName")->firstOrFail();

            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.SUCCESS'), $request->userAgent());

            return response()->json([
                'data' => $parish,
                'version' => $this->VERSION
            ]);
        } catch (ModelNotFoundException $e) {
            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.FAILED'), $request->userAgent());
            return $this->handleException($e, 'parish not found', 404);
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




    public function getParishVillages(Request $request, string $uuid)
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
            $parish = Parish::where('uuid', $uuid)->with("villages:uuid,parishCode,villageName")->firstOrFail();

            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.SUCCESS'), $request->userAgent());

            // Check if villages exist for the parish
            if ($parish->villages) {
                $parish->villages->makeHidden('parishCode');
                return response()->json([
                    'data' => [
                        'county' => [
                            'uuid' => $parish->uuid,
                            'countyName' => $parish->countyName,
                        ],
                        'villages' => $parish->villages,
                    ],
                    'version' => $this->VERSION
                ]);
            }

            // Handle case when no villages are found
            return response()->json(['error' => 'No villages found for the district'], 404);
        } catch (\Throwable $th) {
            //throw $th;

            $this->createRequest($request->url(), $request->ip(), $request->method(), $request->fullUrl(), config('status.FAILED'), $request->userAgent());

            return $this->handleException($th, 'An error occurred', 500);
        }
    }
    //
}
