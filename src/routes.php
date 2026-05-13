<?php

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Wzije\IndoArea\Models\City;
use Wzije\IndoArea\Models\Province;
use Wzije\IndoArea\Models\SubDistrict;
use Wzije\IndoArea\Models\Village;

Route::prefix('api/indo-area')
    ->middleware('api')
    ->group(function () {
        Route::get('/provinces', fn() => Response::json(Province::all(['province_code', 'province_name'])));
        Route::get('/provinces/{code}/cities', fn($code) => Response::json(City::where('city_province_code', $code)->get(['city_code', 'city_name'])));
        Route::get('/cities/{code}/subdistricts', fn($code) => Response::json(SubDistrict::where('sub_district_city_code', $code)->get(['sub_district_code', 'sub_district_name'])));
        Route::get('/subdistricts/{code}/villages', fn($code) => Response::json(Village::where('village_sub_district_code', $code)->get(['village_code', 'village_name'])));
    });
