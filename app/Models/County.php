<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class County extends Model
{
    use HasFactory;

    protected $fillable =["districtCode", "countyCode", "countyName"];

   //belongs to district
   public function district():BelongsTo
   {
       return $this->belongsTo(District::class, 'districtCode');
   }

   //has many subcounties
       /**
        * Get the subcounties associated with the county.
        */
       public function subcounties(): HasMany
       {
           return $this->hasMany(Subcounty::class, 'countyCode');
       }

       //has many parishes
       /**
        * Get the parishes associated with the county.
        */
       public function parishes(): HasMany
       {
           return $this->hasMany(Parish::class, 'countyCode');
       }
       //has many villages
       /**
        * Get the villages associated with the county.
        */
       public function villages(): HasMany
       {
           return $this->hasMany(Village::class, 'countyCode');
       }
   

}
