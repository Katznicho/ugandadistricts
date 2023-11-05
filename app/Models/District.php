<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;

    protected $fillable =["districtCode", "districtName"];

    //has many counties
    /**
     * Get the counties associated with the district.
     */
    public function counties():HasMany
    {
        return $this->hasMany(County::class, 'districtCode', 'districtCode');
    }

    //has many subcounties
    /**
     * Get the subcounties associated with the district.
     */
    public function subcounties():HasMany
    {
        return $this->hasMany(SubCounty::class, 'districtCode', 'districtCode');
    }

    //has many parihes
    //has many parishes
    /**
     * Get the parishes associated with the district.
     */
    public function parishes():HasMany
    {
        return $this->hasMany(Parish::class, 'districtCode', 'districtCode');
    }

    //has many villages
    /**
     * Get the villages associated with the district.
     */
    public function villages(): HasMany
    {
        return $this->hasMany(Village::class, 'districtCode', 'districtCode');
    }
}
