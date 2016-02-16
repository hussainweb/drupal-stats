<?php

/**
 * @file
 */

namespace App\DrupalStats\Controllers\Data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MongoDB\Database;

class ProjectDataController extends Controller
{

    public function __construct()
    {
        $this->middleware('data.cache');
    }

    public function moduleDownloads(Request $request)
    {
        /** @var Database $db */
        $db = DB::getMongoDB();

        $nodes = $db->nodes->find(
          [
              'type' => 'project_module',
          ], [
            'projection' => [
                'title' => 1,
                'field_download_count' => 1,
                'taxonomy_vocabulary_3' => ['$slice' => 1],
                'field_project_machine_name' => 1,
            ],
            'sort' => ['field_download_count' => -1],
            'limit' => 200,
          ]
        )->toArray();

        $terms = [];
        $nodes = array_map(function ($node) use ($db, &$terms) {
            $res = [
                'id' => $node->_id,
                'title' => $node->title,
                'machine_name' => $node->field_project_machine_name,
                'value' => $node->field_download_count,
                'category' => '(not set)',
            ];

            $term = $node->taxonomy_vocabulary_3->getArrayCopy();
            if (isset($term[0]->id) && $tid = $term[0]->id) {
                if (!isset($terms[$tid])) {
                    if ($term = $db->terms->findOne(['_id' => $tid])) {
                        $terms[$tid] = $term;
                    }
                }

                $res['category'] = isset($terms[$tid]) ? $terms[$tid]->name : '(not set)';
            }
            return $res;
        }, $nodes);

        shuffle($nodes);

        return response()->json($nodes);
    }

    public function projectsGrowth()
    {
        /** @var Database $db */
        $db = DB::getMongoDB();

        $nodes = $db->nodes->aggregate([
            [
                '$match' => [
                    'type' => [
                        '$in' => [
                            'project_module',
                            'project_theme',
                            'project_core',
                            'project_distribution',
                            'project_theme_engine',
                        ],
                    ],
                ],
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'created' => 1,
                    'type' => 1,
                    'tsday' => [
                        '$mod' => ['$created', 86400],
                    ],
                ],
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'type' => 1,
                    'ts' => [
                        '$subtract' => ['$created', '$tsday'],
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => [
                        'day' => '$ts',
                        'project_type' => '$type',
                    ],
                    'count' => ['$sum' => 1],
                ],
            ],
            [
                '$sort' => ['_id.day' => 1],
            ],
        ]);

        $projects = [
            'project_module' => [],
            'project_theme' => [],
            'project_core' => [],
            'project_distribution' => [],
            'project_theme_engine' => [],
        ];
        $last_timestamps = [
            'project_module' => 0,
            'project_theme' => 0,
            'project_core' => 0,
            'project_distribution' => 0,
            'project_theme_engine' => 0,
        ];

        $min_timestamp = mktime(0, 0, 0, 1, 1, 2000);
        foreach ($nodes as $node) {
            $type = $node->_id->project_type;

            // We can just save the last timestamp per each project type as the
            // list is sorted by timestamp anyway.
            $last_timestamps[$type] += $node->count;

            // We don't return data for before 2000.
            if ($node->_id->day < $min_timestamp) {
                continue;
            }

            $projects[$type][date('Y-m-d', $node->_id->day)] = $last_timestamps[$type];
        }

        return response()->json($projects);
    }
}
