<?php

namespace App\DrupalStats\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MongoDB\Database;

class ProjectController extends Controller
{

    public function __construct()
    {
//        $this->middleware('data.cache');
    }

    public function projectInfo($name)
    {
        /** @var Database $db */
        $db = DB::getMongoDB();

        $nodes = $db->nodes->find(
            [
                'type' => ['$in' => [
                    'project_module',
                    'project_theme',
                    'project_core',
                    'project_distribution',
                    'project_theme_engine',
                    'project_drupalorg',
                ]],
                'field_project_machine_name' => $name,
            ]
        )->toArray();

        if (count($nodes) == 0) {
            abort(404);
        }

        $node = reset($nodes);

        return $node;
    }
}
