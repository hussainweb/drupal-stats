<?php

/**
 * @file
 */

namespace App\DrupalStats\Models\Repositories;

use App\DrupalStats\Models\Entities\FieldCollectionOrganization;

class FieldOrganizationRepository extends RepositoryBase
{

    /**
     * {@inheritdoc}
     */
    protected function getModel()
    {
        return new FieldCollectionOrganization();
    }

    /**
     * {@inheritdoc}
     */
    protected function processValue($key, $value)
    {
        $keys_make_int = [
            'item_id',
        ];

        if ($key == 'host_entity') {
            unset($value->uri);
        }
        elseif ($key == 'field_organization_reference') {
            unset($value->uri);
            unset($value->resource);
        }
        elseif (in_array($key, $keys_make_int)) {
            $value = (int) $value;
        }

        return parent::processValue($key, $value);
    }
}
