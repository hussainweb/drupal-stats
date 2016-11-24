<?php

namespace App\DrupalStats\Transformers;

use App\DrupalStats\Models\Entities\Node;
use App\DrupalStats\Models\Entities\Term;
use League\Fractal\TransformerAbstract;

class NodeTransformer extends TransformerAbstract
{

    protected function processTaxonomyVocabulary(Node $node, $vid)
    {
        $field_name = 'taxonomy_vocabulary_' . $vid;
        if (empty($node->$field_name)) {
            return null;
        }

        $term = Term::find($node->$field_name['id']);
        if (!$term) {
            return null;
        }

        return $this->item($term, new TermDataTransformer());
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
