<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;

class AttributeController extends Controller
{
    public function getCountry()
    {
        return successResponse(Country::all());
    }
    public function getCity()
    {
        return successResponse(City::where('country_id', 2)->get());
    }

  
}
