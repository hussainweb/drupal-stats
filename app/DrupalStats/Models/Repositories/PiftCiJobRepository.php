<?php

/**
 * @file
 */

namespace App\DrupalStats\Models\Repositories;

use App\DrupalStats\Models\Entities\PiftCiJob;

class PiftCiJobRepository extends RepositoryBase
{

    /**
     * {@inheritdoc}
     */
    protected function getModel()
    {
        return new PiftCiJob();
    }

    /**
     * {@inheritdoc}
     */
    protected function processValue($key, $value)
    {
        $keys_make_int = [
          'created',
          'changed',
        ];

        if (in_array($key, $keys_make_int)) {
            $value = (int) $value;
        }

        return parent::processValue($key, $value);
    }
}
