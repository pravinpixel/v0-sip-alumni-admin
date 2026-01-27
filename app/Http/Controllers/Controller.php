<?php

namespace App\Http\Controllers;

use App\Models\CenterLocations;
use App\Models\Cities;
use App\Models\Pincodes;
use App\Models\PixelLocation;
use App\Models\States;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $error;

    public function returnError($errors = false, $message = 'Error', $code = 400)
    {
        return response([
            'success' => false,
            'message' => $message,
            'error' => $errors
        ], $code);
    }
    public function returnSuccess($data, $message = 'Success')
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    public function importData()
    {
        States::truncate();
        Cities::truncate();
        Pincodes::truncate();
        CenterLocations::truncate();
        // ==========
        // 1. IMPORT STATES
        // ==========
        $states = PixelLocation::whereNotNull('state')
            ->where('state', '!=', '')
            ->select('state')
            ->distinct()
            ->get();

        foreach ($states as $row) {
            $stateName = trim($row->state);

            States::firstOrCreate(
                ['name' => $stateName],
                ['country_id' => 101, 'status' => 1]
            );
        }

        // ==========
        // 2. IMPORT CITIES (mapping with states)
        // ==========
        $cities = PixelLocation::whereNotNull('city')
            ->where('city', '!=', '')
            ->select('city', 'state')
            ->distinct()
            ->get();

        foreach ($cities as $row) {
            $cityName = trim($row->city);
            $stateName = trim($row->state);

            $state = States::where('name', $stateName)->first();

            if ($state) {
                Cities::firstOrCreate(
                    ['name' => $cityName, 'state_id' => $state->id],
                    ['status' => 1]
                );
            }
        }

        // ==========
        // 3. IMPORT PINCODES (mapping with cities)
        // ==========
        $pincodes = PixelLocation::whereNotNull('zip')
            ->where('zip', '!=', '')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->select('zip', 'city')
            ->distinct()
            ->get();

        foreach ($pincodes as $row) {
            $cityName = trim($row->city);
            $pincodeVal = preg_replace('/\D/', '', $row->zip);

            // Validate length (India specific)
            if (strlen($pincodeVal) !== 6) {
                continue; // skip wrong data
            }

            $city = Cities::where('name', $cityName)->first();
            if (!$city) continue;

            if ($city) {
                Pincodes::firstOrCreate(
                    ['pincode' => $pincodeVal, 'city_id' => $city->id]
                );
            }
        }

        // 4. IMPORT AREAS / LOCATIONS (mapping with cities)
        // ==========
$rows = PixelLocation::whereNotNull('prefered_center_location')
    ->where('prefered_center_location', '!=', '')
    ->whereNotNull('zip')
    ->select('city', 'prefered_center_location', 'zip')
    ->distinct()
    ->get();

foreach ($rows as $row) {

    // 1. Clean ZIP to 6 digit
    $zip = preg_replace('/\D/', '', $row->zip);
    if (strlen($zip) !== 6) continue;

    // 3. Resolve city via pincode
    $cityName = trim($row->city);
    $city = Cities::where('name', $cityName)->first();
    if (!$city) continue;
    // 2. Resolve pincode
    $pincode = Pincodes::where('pincode', $zip)->where('city_id', $city->id)->first();
    if (!$pincode) continue;  // cannot map city


    // 4. Create center
    CenterLocations::firstOrCreate(
        [
            'name' => trim($row->prefered_center_location),
            'city_id' => $city->id,
            'pincode_id' => $pincode->id
        ],
        [
            'is_custom' => 0
        ]
    );
}



        return $this->returnSuccess([], 'Import completed successfully.');
    }
}
