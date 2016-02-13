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
}
