<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parish extends Model
{
    use HasFactory;
    protected $fillable =["districtCode", "countyCode", "subCountyCode","parishCode", "parishName", "uuid"];

    //belongs to district
    /**
     * Get the district associated with the parish.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'districtCode', "districtCode" );
    }

    //belongs to county
    /**
     * Get the county associated with the parish.
     */
    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class, 'countyCode', "countyCode");
    }

    //belongs to subcounty
    /**
     * Get the subcounty associated with the parish.
     */
    public function subcounty(): BelongsTo
    {
        return $this->belongsTo(SubCounty::class, 'subCountyCode', "subCountyCode");
    }
    //has many villages
    /**
     * Get the villages associated with the parish.
     */
    public function villages(): HasMany
    {
        return $this->hasMany(Village::class, 'parishCode', 'parishCode');
    }
}
