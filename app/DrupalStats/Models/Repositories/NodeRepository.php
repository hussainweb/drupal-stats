<?php

/**
 * @file
 */

namespace App\DrupalStats\Models\Repositories;

use App\DrupalStats\Models\Entities\Node;

class NodeRepository extends RepositoryBase
{

    public $terms = [];
    public $releases = [];

    /**
     * {@inheritdoc}
     */
    protected function getModel()
    {
        return new Node();
    }

    /**
     * {@inheritdoc}
     */
    protected function processValue($key, $value)
    {
        $keys_files = [
            'upload',
            'field_project_images',
            'field_issue_files',
        ];

        $keys_nullify = [
            'body',
            'field_project_issue_guidelines',
            'field_contributions',
            'field_organization_training_desc',
            'field_developed',
            'field_goals',
            'field_module_selection',
            'field_overview',
        ];

        $keys_references = [
            'author',
            'book',
            'field_release_project',
            'book_ancestors',
            'field_release_files',
            'comments',
            'field_module',
        ];

        $keys_make_int = [
            'nid',
            'field_download_count',
            'created',
            'changed',
            'comment_count',
            'comment_count_new',
        ];

        $keys_make_bool = [
            'status',
            'promote',
            'sticky',
        ];

        if (strpos($key, "taxonomy_vocabulary_") === 0) {
            if (is_array($value)) {
                foreach ($value as $i => $term_item) {
                    $this->terms[$term_item->id] = $term_item->id;
                    unset($value[$i]->uri);
                    unset($value[$i]->resource);
                }
            }
            else {
                $this->terms[$value->id] = $value->id;
                unset($value->uri);
                unset($value->resource);
            }
        }
        elseif (in_array($key, $keys_nullify)) {
            $value = null;
        }
        elseif (in_array($key, $keys_references)) {
            if (is_array($value)) {
                foreach ($value as $i => $item) {
                    if ($key == 'field_release_files') {
                        $this->releases[$item->id] = $item->id;
                    }
                    unset($value[$i]->uri);
                    unset($value[$i]->resource);
                }
            }
            else {
                unset($value->uri);
                unset($value->resource);
            }
        }
        elseif (in_array($key, $keys_files)) {
            foreach ($value as $i => $item) {
                if (isset($value[$i]->file)) {
                    unset($value[$i]->file->uri);
                    unset($value[$i]->file->resource);
                }
            }
        }
        elseif (in_array($key, $keys_make_int)) {
            $value = (int) $value;
        }
        elseif (in_array($key, $keys_make_bool)) {
            $value = (bool) $value;
        }

        return parent::processValue($key, $value);
    }
}
