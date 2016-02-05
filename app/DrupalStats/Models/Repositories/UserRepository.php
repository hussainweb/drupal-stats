<?php

/**
 * @file
 */

namespace App\DrupalStats\Models\Repositories;

use App\DrupalStats\Models\Entities\User;

class UserRepository extends RepositoryBase
{

    /**
     * {@inheritdoc}
     */
    protected function getModel()
    {
        return new User();
    }

    /**
     * {@inheritdoc}
     */
    protected function processValue($key, $value)
    {
        $keys_references = [
          'field_organizations',
          'field_areas_of_expertise',
          'field_mentors',
        ];

        if (in_array($key, $keys_references)) {
            foreach ($value as $i => $item) {
                unset($value[$i]->uri);
                unset($value[$i]->resource);
            }
        }

        return parent::processValue($key, $value);
    }
}
