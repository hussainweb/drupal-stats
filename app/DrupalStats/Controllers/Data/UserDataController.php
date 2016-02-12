<?php

/**
 * @file
 */

namespace App\DrupalStats\Controllers\Data;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use MongoDB\Database;

class UserDataController extends Controller
{

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
}
