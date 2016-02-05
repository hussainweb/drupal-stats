<?php

/**
 * @file
 */

namespace App\DrupalStats\Models\Repositories;

use App\DrupalStats\Models\Entities\Term;

class TermRepository extends RepositoryBase
{

    /**
     * {@inheritdoc}
     */
    protected function getModel()
    {
        return new Term();
    }

    /**
     * {@inheritdoc}
     */
    protected function processValue($key, $value)
    {
        if ($key == 'parent') {
            unset($value->uri);
            unset($value->resource);
        }
        elseif ($key == 'parents_all') {
            foreach ($value as $i => $item) {
                unset($value[$i]->uri);
                unset($value[$i]->resource);
            }
        }

        return parent::processValue($key, $value);
    }
}
