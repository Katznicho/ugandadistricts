<?php

use App\Http\Controllers\CountyController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\ParishController;
use App\Http\Controllers\SubCountyController;
use App\Http\Controllers\VillageController;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('uganda/data/v1')->group(function () {
    //district
    Route::get("/districts", [DistrictController::class, 'getDistricts']);
    Route::get("/district/{uuid}", [DistrictController::class, 'getDistrictByUUID']);
    Route::get("/district/{uuid}/counties", [DistrictController::class, 'getDistrictCounties']);
    Route::get("/district/{uuid}/subcounties", [DistrictController::class, 'getDistrictSubCounties']);
    Route::get("/district/{uuid}/parishes", [DistrictController::class, 'getDistrictParishes']);
    Route::get("/district/{uuid}/villages", [DistrictController::class, 'getDistrictVillages']);

    //district

    Route::get("/counties", [CountyController::class, 'getCounties']);
    Route::get("/subcounties", [SubCountyController::class, 'getSubCounties']);
    Route::get("/parishes", [ParishController::class, 'getParishes']);
    Route::get("/villages", [VillageController::class, 'getVillages']);
});

// Route::get("district/{id}", function (Request $request, string $id) {
//     return District::findOrFail($id)->with("counties");
// });


Route::get("district/{id}", function (Request $request, string $id) {
    $district = District::where('uuid', $id)->with("counties")->firstOrFail();

    if (!$district) {
        return response()->json(['error' => 'District not found'], 404);
    }

    return response()->json($district);
});
