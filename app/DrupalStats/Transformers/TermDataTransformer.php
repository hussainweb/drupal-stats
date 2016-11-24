<?php

namespace App\DrupalStats\Transformers;

use App\DrupalStats\Models\Entities\Term;
use League\Fractal\TransformerAbstract;

class TermDataTransformer extends TransformerAbstract
{

    public function transform(Term $term)
    {
        return [
            'tid' => $term->tid,
            'name' => $term->name,
            'description' => $term->description,
        ];
    }
}
