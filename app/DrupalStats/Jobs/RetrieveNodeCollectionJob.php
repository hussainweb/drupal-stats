<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

use App\DrupalStats\Models\Entities\Node as NodeModel;
use App\DrupalStats\Models\Entities\Term;
use Hussainweb\DrupalApi\Client;
use Hussainweb\DrupalApi\Entity\Node;
use Hussainweb\DrupalApi\Request\Collection\NodeCollectionRequest;
use Hussainweb\DrupalApi\Request\TaxonomyTermRequest;
use Jenssegers\Mongodb\Eloquent\Model;

class RetrieveNodeCollectionJob extends RetrieveJobBase
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo "Retrieving " . (string) $this->request->getUri() . ".\n";

        $client = new Client();
        $collection = $client->getEntity($this->request);
        $terms = [];

        /** @var Node $node */
        foreach ($collection as $node) {
            $this->saveDataToModel($node, new NodeModel(), function ($key, $value) use (&$terms) {
                if (strpos($key, "taxonomy_vocabulary_") === 0) {
                    if (is_array($value)) {
                        foreach ($value as $i => $term_item) {
                            $terms[$term_item->id] = $term_item->id;
                            unset($value[$i]->uri);
                            unset($value[$i]->resource);
                        }
                    }
                    else {
                        $terms[$value->id] = $value->id;
                        unset($value->uri);
                        unset($value->resource);
                    }
                }
                elseif ($key == 'body' || $key == 'field_project_issue_guidelines') {
                    $value = null;
                }
                elseif ($key == 'author' || $key == 'book' || $key == 'field_release_project') {
                    unset($value->uri);
                    unset($value->resource);
                }
                elseif ($key == 'book_ancestors' || $key == 'field_release_files' || $key == 'comments') {
                    foreach ($value as $i => $items) {
                        unset($value[$i]->uri);
                        unset($value[$i]->resource);
                    }
                }
                elseif ($key == 'upload' || $key == 'field_project_images') {
                    foreach ($value as $i => $items) {
                        unset($value[$i]->file->uri);
                        unset($value[$i]->file->resource);
                    }
                }

                return $value;
            });
        }

        foreach ($terms as $tid) {
            echo "Encountered term " . $tid . "...\n";
            if (is_null(Term::find($tid))) {
                echo "Queueing term " . $tid . "...\n";
                $this->dispatch(new RetrieveTermJob(new TaxonomyTermRequest($tid)));
            }
        }

        if ($next_url = $collection->getNextLink()) {
            $next_url_params = [];
            parse_str($next_url->getQuery(), $next_url_params);
            $this->dispatch(new RetrieveNodeCollectionJob(new NodeCollectionRequest($next_url_params)));
        }
    }
}
