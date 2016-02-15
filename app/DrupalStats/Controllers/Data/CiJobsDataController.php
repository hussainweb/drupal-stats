<?php

/**
 * @file
 */

namespace App\DrupalStats\Controllers\Data;

use App\DrupalStats\Jobs\RetrievePiftCiJobCollectionJob;
use App\DrupalStats\Models\Services\JobStatusService;
use App\Http\Controllers\Controller;
use Hussainweb\DrupalApi\Request\Collection\PiftCiJobCollectionRequest;
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

        sort($statuses);
        sort($branches);

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

    public function cijobsBranchReason()
    {
        /** @var Database $db */
        $db = DB::getMongoDB();

        $rows = $db->pift_ci_jobs->aggregate([
            [
                '$group' => [
                    '_id' => [
                        'branch' => '$core_branch',
                        'reason' => '$reason',
                    ],
                    'count' => [
                        '$sum' => 1,
                    ],
                ],
            ],
        ])->toArray();

        $reasons = array_unique(array_map(function ($row) {
            return $row->_id->reason ?: 'not-set';
        }, $rows));
        $branches = array_unique(array_map(function ($row) {
            return $row->_id->branch ?: 'not-set';
        }, $rows));

        sort($reasons);
        sort($branches);

        $data = [];
        foreach ($branches as $branch) {
            foreach ($reasons as $reason) {
                $data[$branch][$reason] = [
                    'branch' => $branch,
                    'reason' => $reason,
                    'count' => 0,
                ];
            }
        }

        foreach ($rows as $row) {
            $branch = $row->_id->branch ?: 'not-set';
            $reason = $row->_id->reason ?: 'not-set';

            $data[$branch][$reason] = [
                'branch' => $branch,
                'reason' => $reason,
                'count' => $row->count,
            ];
        }

        $data = array_map(function ($data) {
            return array_values($data);
        }, $data);

        return response()->json([
            'branches' => array_values($branches),
            'reasons' => array_values($reasons),
            'data' => array_values($data),
        ]);
    }

    public function cijobsRefresh(JobStatusService $job_status_service)
    {
        $job = $job_status_service->getJobStatus('pift_ci_jobs', function () {
            /** @var Database $db */
            $db = DB::getMongoDB();

            $last_job = $db->pift_ci_jobs->findOne([], [
                'sort' => ['updated' => -1],
                'limit' => 1,
            ]);
            return $last_job->updated;
        });

        if (!empty($job->queued)) {
            return response()->json([
                'message' => 'The request is already queued. You should see updates soon.',
            ])->setStatusCode(406);
        }

        $req = new PiftCiJobCollectionRequest([
            'sort' => 'updated',
            'direction' => 'DESC',
        ]);
        $options = [
          'last_updated' => $job->last_updated,
        ];
        $this->dispatch(new RetrievePiftCiJobCollectionJob($req, $options));

        $job->queued = true;
        $job->save();

        return response()->json([
            'message' => 'We have queued the request. You should see updates soon.',
        ])->setStatusCode(202);
    }
}
