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
        if ($key == 'host_entity') {
            unset($value->uri);
        }
        elseif ($key == 'field_release_file' && isset($value->file)) {
            unset($value->file->uri);
            unset($value->file->resource);
        }
        elseif ($key == 'field_release_file_downloads') {
            $value = (int) $value;
        }

        return parent::processValue($key, $value);
    }
}
