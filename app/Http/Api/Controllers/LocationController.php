<?php

namespace App\Http\Api\Controllers;

use App\Location;

class LocationController
{

    public function __construct()
    {
    }

    /**
     * @OA\Get(
     *     path="/location",
     *     description="Возвращает данные о текущей локации.",
     *     tags={"location"},
     * @OA\Response(response="200", description="Возвращает данные о локации."),
     * @OA\Response(response="404", description="Локация не найдена.")
     * )
     */
    public function getLocation(string $locationId)
    {
        $location = Location::query()->find($locationId);
        if (!$location) {
            return response()->json(['message' => 'Location not found.']);
        }
        return response()->json(['location' => $location], 200);
    }

    /**
     * @OA\Get(
     *     path="/location/all",
     *     description="Возвращает данные о всех лоакациях.",
     *     tags={"location"},
     * @OA\Response(response="200", description="Возвращает список лоакаций.")
     * )
     */
    public function getLocations()
    {
        $locations = Location::query()->get(['id','name','description']);
        return response()->json(['locations' => $locations], 200);
    }
}
