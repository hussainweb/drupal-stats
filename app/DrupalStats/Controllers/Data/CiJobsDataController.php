<?php

/**
 * @file
 */

namespace App\DrupalStats\Controllers\Data;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use MongoDB\Database;

class CiJobsDataController extends Controller
{

    public function cijobsBranchStatus()
    {
        /** @var Database $db */
        $db = DB::getMongoDB();

        $rows = $db->pift_ci_jobs->aggregate([
            [
                '$group' => [
                    '_id' => [
                        'branch' => '$core_branch',
                        'status' => '$status',
                    ],
                    'count' => [
                        '$sum' => 1,
                    ],
                ],
            ],
        ])->toArray();

        $statuses = array_unique(array_map(function ($row) {
            return $row->_id->status;
        }, $rows));
        $branches = array_unique(array_map(function ($row) {
            return $row->_id->branch ?: 'not-set';
        }, $rows));

        $data = [];
        foreach ($statuses as $status) {
            foreach ($branches as $branch) {
                $data[$status][$branch] = [
                    'branch' => $branch,
                    'status' => $status,
                    'count' => 0,
                ];
            }
        }

        foreach ($rows as $row) {
            $branch = $row->_id->branch ?: 'not-set';
            $status = $row->_id->status;

            $data[$status][$branch] = [
                'branch' => $branch,
                'status' => $status,
                'count' => $row->count,
            ];
        }

        $data = array_map(function ($data) {
            return array_values($data);
        }, $data);

        return response()->json([
            'branches' => array_values($branches),
            'status' => array_values($statuses),
            'data' => array_values($data),
        ]);
    }
}
