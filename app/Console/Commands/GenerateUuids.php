<?php

namespace App\Console\Commands;

use App\Models\County;
use App\Models\District;
use App\Models\Parish;
use App\Models\SubCounty;
use App\Models\Village;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateUuids extends Command
{
    protected $signature = 'generate:uuids';
    protected $description = 'Generate UUIDs for existing districts';

    public function handle()
    {
        // $districts = District::all();

        // foreach ($districts as $district) {
        //     $district->uuid = (string) Str::uuid();
        //     $district->save();
        // }

        //generate uuids for counties
        $counties = County::all();

        foreach ($counties as $county) {
            $county->uuid = (string) Str::uuid();
            $county->save();
        }

        //generate uuids for subcounties
        $subcounties = SubCounty::all();

        foreach ($subcounties as $subcounty) {
            $subcounty->uuid = (string) Str::uuid();
            $subcounty->save();
        }
        //generate uuids for parishes
        $parishes = Parish::all();

        foreach ($parishes as $parish) {
            $parish->uuid = (string) Str::uuid();
            $parish->save();
        }
        //generate uuids for villages
        $villages = Village::all();

        foreach ($villages as $village) {
            $village->uuid = (string) Str::uuid();
            $village->save();
        }

        $this->info('UUIDs generated successfully for existing data.');
    }
}
