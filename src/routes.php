<?php

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Wzije\IndoArea\Models\District;
use Wzije\IndoArea\Models\Province;
use Wzije\IndoArea\Models\Regency;
use Wzije\IndoArea\Models\Village;

Route::prefix('api/indo-area')
    ->middleware('api')
    ->group(function () {
        Route::get('/provinces', fn() => Response::json(Province::all(['id', 'name'])));
        Route::get('/provinces/{id}/regencies', fn($id) => Response::json(Regency::where('province_id', $id)->get(['id', 'name'])));
        Route::get('/regencies/{id}/districts', fn($id) => Response::json(District::where('regency_id', $id)->get(['id', 'name'])));
        Route::get('/districts/{id}/villages', fn($id) => Response::json(Village::where('district_id', $id)->get(['id', 'name'])));
    });
