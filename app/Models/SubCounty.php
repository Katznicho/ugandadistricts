<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubCounty extends Model
{
    use HasFactory;

    protected $fillable =["districtCode", "countyCode", "subCountyCode", "subCountyName"];

    //belongs to district
    public function district():BelongsTo
    {
        return $this->belongsTo(District::class, 'districtCode');
    }

    //belongs to county
    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class, 'countyCode');
    }
    //has many parishes
    /**
     * Get the parishes associated with the subcounty.
     */
    public function parishes(): HasMany
    {
        return $this->hasMany(Parish::class, 'subCountyCode');
    }

    //has many villages
    /**
     * Get the villages associated with the subcounty.
     */
    public function villages(): HasMany
    {
        return $this->hasMany(Village::class, 'subCountyCode');
    }
}
