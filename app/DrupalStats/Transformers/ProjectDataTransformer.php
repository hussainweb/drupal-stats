<?php

namespace App\DrupalStats\Transformers;

use App\DrupalStats\Models\Entities\Node;
use App\DrupalStats\Models\Entities\Term;
use League\Fractal\TransformerAbstract;

class ProjectDataTransformer extends TransformerAbstract
{

    protected $defaultIncludes = [
        'moduleCategories',
        'maintenanceStatus',
        'developmentStatus',
    ];

    public function transform(Node $node)
    {
        return [
            'nid' => $node->nid,
            'type' => $node->type,
            'title' => $node->title,
            'machine_name' => $node->field_project_machine_name,
            'project_type' => $node->field_project_type,
            'issue_queue' => $node->field_project_has_issue_queue,
            'components' => $node->field_project_components,
            'releases' => $node->field_project_has_releases,
            'downloads' => $node->field_download_count,
            'demo' => $node->field_project_demo['url'] ?? '',
            'documentation' => $node->field_project_documentation['url'] ?? '',
            'homepage' => $node->field_project_homepage['url'] ?? '',
            'created' => date('c', $node->created),
            'changed' => date('c', $node->changed),
        ];
    }

    public function includeModuleCategories(Node $node)
    {
        return $this->processTaxonomyVocabularyArray($node, '3');
    }

    public function includeMaintenanceStatus(Node $node)
    {
        return $this->processTaxonomyVocabulary($node, '44');
    }

    public function includeDevelopmentStatus(Node $node)
    {
        return $this->processTaxonomyVocabulary($node, '46');
    }

    protected function processTaxonomyVocabulary(Node $node, $vid)
    {
        $field_name = 'taxonomy_vocabulary_' . $vid;
        if (empty($node->$field_name)) {
            return null;
        }

        return $this->item(Term::find($node->$field_name['id']), new TermDataTransformer());
    }

    protected function processTaxonomyVocabularyArray(Node $node, $vid)
    {
        $field_name = 'taxonomy_vocabulary_' . $vid;
        if (empty($node->$field_name)) {
            return null;
        }

        $ids = array_map(function ($item) {
            return $item['id'];
        }, $node->$field_name);

        return $this->collection(Term::whereIn('_id', $ids)->get(), new TermDataTransformer());
    }
}
