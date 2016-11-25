<?php

namespace App\DrupalStats\Controllers\Api;

use App\DrupalStats\Models\Entities\Node;
use App\DrupalStats\Transformers\ProjectDataTransformer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class ProjectController extends Controller
{

    /**
     * @var Manager
     */
    protected $fractalManager;

    public function __construct(Manager $fractal_manager, Request $request)
    {
        $this->middleware('data.cache');

        $this->fractalManager = $fractal_manager;
        $include = $request->query->get('include');
        if ($include) {
            $this->fractalManager->parseIncludes($request->query->get('include'));
        }
    }

    public function projectInfo($name)
    {
        $node = Node::where('field_project_machine_name', $name)
            ->whereIn('type', [
                'project_module',
                'project_theme',
                'project_core',
                'project_distribution',
                'project_theme_engine',
                'project_drupalorg',
            ])
            ->first();

        if (!$node) {
            abort(404);
        }

        $resource = new Item($node, new ProjectDataTransformer());

        return $this->fractalManager->createData($resource)->toArray();
    }
}
