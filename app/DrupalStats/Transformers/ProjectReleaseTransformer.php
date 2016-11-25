<?php

namespace App\DrupalStats\Transformers;

use App\DrupalStats\Models\Entities\Node;

class ProjectReleaseTransformer extends NodeTransformer
{

    protected $availableIncludes = [
        'coreCompatibility',
        'releaseType',
    ];

    public function transform(Node $node)
    {
        return [
            'nid' => $node->nid,
            'title' => $node->title,
            'version' => [
                'full' => $node->field_release_version,
                'major' => $node->field_release_version_major,
                'minor' => $node->field_release_version_minor,
                'patch' => $node->field_release_version_patch,
                'extra' => $node->field_release_version_extra,
                'vcs_label' => $node->field_release_vcs_label,
            ],
            'created' => date('c', $node->created),
            'changed' => date('c', $node->changed),
        ];
    }

    public function includeCoreCompatibility(Node $node)
    {
        return $this->processTaxonomyVocabulary($node, '6');
    }

    public function includeReleaseType(Node $node)
    {
        return $this->processTaxonomyVocabularyArray($node, '7');
    }
}
