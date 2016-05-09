<?php

/**
 * @file
 */

namespace App\DrupalStats\Models\Repositories;

use App\DrupalStats\Models\Entities\FieldCollectionRelease;

class FieldReleaseRepository extends RepositoryBase
{

    /**
     * {@inheritdoc}
     */
    protected function getModel()
    {
        return new FieldCollectionRelease();
    }

    /**
     * {@inheritdoc}
     */
    protected function processValue($key, $value)
    {
        $keys_make_int = [
            'item_id',
            'field_release_file_downloads',
        ];

        if ($key == 'host_entity') {
            unset($value->uri);
        }
        elseif ($key == 'field_release_file' && isset($value->file)) {
            unset($value->file->uri);
            unset($value->file->resource);
        }
        elseif (in_array($key, $keys_make_int)) {
            $value = (int) $value;
        }

        return parent::processValue($key, $value);
    }
}
