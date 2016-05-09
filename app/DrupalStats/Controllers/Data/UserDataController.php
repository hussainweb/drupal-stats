<?php

/**
 * @file
 */

namespace App\DrupalStats\Controllers\Data;

use App\DrupalStats\Models\Services\CountryHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use MongoDB\Database;

class UserDataController extends Controller
{

    public function __construct()
    {
        $this->middleware('data.cache');
    }

    public function userLanguages()
    {
        /** @var Database $db */
        $db = DB::getMongoDB();

        $languages = $db->users->aggregate([
            [
                '$unwind' => '$field_languages',
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'field_languages' => 1,
                ],
            ],
            [
                '$group' => [
                    '_id' => '$field_languages',
                    'count' => ['$sum' => 1],
                ],
            ],
            [
                '$sort' => ['count' => -1],
            ],
        ])->toArray();

        $languages = array_filter($languages, function ($val) {
            return $val['_id'] ? true : false;
        });

        $languages = array_map(function ($row) {
            return [
                'language' => $row->_id,
                'count' => $row->count,
            ];
        }, $languages);

        return response()->json(array_values($languages));
    }

    public function userExpertise()
    {
        /** @var Database $db */
        $db = DB::getMongoDB();

        $expertise = $db->users->aggregate([
            [
                '$unwind' => '$field_areas_of_expertise',
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'field_areas_of_expertise' => 1,
                ],
            ],
            [
                '$group' => [
                    '_id' => '$field_areas_of_expertise.id',
                    'count' => ['$sum' => 1],
                ],
            ],
            [
                '$match' => [
                    'count' => ['$gte' => 2],
                ],
            ],
            [
                '$sort' => ['count' => -1],
            ],
        ])->toArray();

        $expertise = array_map(function ($row) use ($db) {
            $term = $db->terms->findOne(['_id' => $row->_id]);
            return [
                'expertise' => $term ? $term->name : '',
                'count' => $row->count,
            ];
        }, $expertise);

        $expertise = array_filter($expertise, function ($val) {
            return $val['expertise'] ? true : false;
        });

        return response()->json(array_values($expertise));
    }

    public function userCountries()
    {
        /** @var Database $db */
        $db = DB::getMongoDB();

        $country_list = new CountryHelper();

        $countries = $db->users->aggregate([
            [
                '$group' => [
                    '_id' => '$field_country',
                    'count' => ['$sum' => 1],
                ],
            ],
            [
                '$sort' => ['count' => -1],
            ],
        ])->toArray();

        $countries = array_map(function ($row) use ($db, $country_list) {
            return [
                'country' => $country_list->getCountryCode($row->_id),
                'countryName' => $row->_id,
                'count' => $row->count,
            ];
        }, $countries);

        $countries = array_filter($countries, function ($val) {
            return $val['country'] ? true : false;
        });

        return response()->json(array_values($countries));
    }

    public function userGrowth()
    {
        /** @var Database $db */
        $db = DB::getMongoDB();

        $users = $db->users->aggregate([
            [
                '$project' => [
                    '_id' => 0,
                    'created' => 1,
                    'tsday' => [
                        '$mod' => ['$created', 86400],
                    ],
                ],
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'ts' => [
                        '$subtract' => ['$created', '$tsday'],
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => [
                        'day' => '$ts',
                    ],
                    'count' => ['$sum' => 1],
                ],
            ],
            [
                '$sort' => ['_id.day' => 1],
            ],
        ]);

        $users_count = [];
        $last_timestamp_count = 0;

        $min_timestamp = mktime(0, 0, 0, 1, 1, 2000);
        foreach ($users as $user) {
            // We can just save the last timestamp per each project type as the
            // list is sorted by timestamp anyway.
            $last_timestamp_count += $user->count;

            // We don't return data for before 2000.
            if ($user->_id->day < $min_timestamp) {
                continue;
            }

            $users_count[date('Y-m-d', $user->_id->day)] = $last_timestamp_count;
        }

        return response()->json(['total' => $users_count]);
    }
}
