<?php

/**
 * @file
 */

namespace App\DrupalStats\Models\Repositories;

use App\DrupalStats\Models\Entities\User;

class UserRepository extends RepositoryBase
{

    public $organizations = [];

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
            foreach ((array) $value as $i => $item) {
                if ($key == 'field_organizations') {
                    $this->organizations[$item->id] = $item->id;
                }
                unset($value[$i]->uri);
                unset($value[$i]->resource);
            }
        }
        elseif ($key == 'created') {
            $value = (int) $value;
        }

        return parent::processValue($key, $value);
    }
}
