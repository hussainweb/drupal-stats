<?php

namespace App\DrupalStats\Transformers;

use App\DrupalStats\Models\Entities\Node;

class ProjectDataTransformer extends NodeTransformer
{

    protected $defaultIncludes = [
        'moduleCategories',
        'maintenanceStatus',
        'developmentStatus',
        'releases',
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

    public function includeReleases(Node $node)
    {
        $releases = Node::where('type', 'project_release')
            ->where('field_release_project.id', (string) $node->nid)
            ->get();
        return $this->collection($releases, new ProjectReleaseTransformer());
    }
}
