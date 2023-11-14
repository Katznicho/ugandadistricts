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

    //county
    Route::get("/counties", [CountyController::class, 'getCounties']);
    Route::get("/county/{uuid}", [CountyController::class, 'getCountyByUUID']);
    Route::get("/county/{uuid}/subcounties", [CountyController::class, 'getCountySubCounties']);
    Route::get("/county/{uuid}/parishes", [CountyController::class, 'getCountyParishes']);
    Route::get("/county/{uuid}/villages", [CountyController::class, 'getCountyVillages']);
    //county

    //county and ditrict
    //get subcounties by both county and district
    Route::get("/subcounties/{districtuuid}/district/{countyuuid}/county", [CountyController::class, 'getSubCountiesByCountyAndDistrict']);
    //get parish by both county and district
    // Route::get("/parishes/{parishuuid}/district/{uuid}/county", [CountyController::class, 'getParishesByCountyAndDistrict']);
    // //get villages by both county and district
    // Route::get("/villages/{uuid}/district/{uuid}/county", [CountyController::class, 'getVillagesByCountyAndDistrict']);
    // //county and district

    //subcounty
    Route::get("/subcounties", [SubCountyController::class, 'getSubCounties']);
    Route::get("/subcounty/{uuid}", [SubCountyController::class, 'getSubCountyByUUID']);
    Route::get("/subcounty/{uuid}/parishes", [SubCountyController::class, 'getSubCountyParishes']);
    Route::get("/subcounty/{uuid}/villages", [SubCountyController::class, 'getSubCountyVillages']);
    //subcounty

    //parish
    Route::get("/parishes", [ParishController::class, 'getParishes']);
    Route::get("/parish/{uuid}", [ParishController::class, 'getParishByUUID']);
    Route::get("/parish/{uuid}/villages", [ParishController::class, 'getParishVillages']);
    //parish

    //village
    Route::get("/villages", [VillageController::class, 'getVillages']);
    Route::get("/village/{uuid}", [VillageController::class, 'getVillageByUUID']);
    //village
});




Route::get("district/{id}", function (Request $request, string $id) {
    $district = District::where('uuid', $id)->with("counties")->firstOrFail();

    if (!$district) {
        return response()->json(['error' => 'District not found'], 404);
    }

    return response()->json($district);
});
