<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

use App\DrupalStats\Models\Entities\Term;
use Hussainweb\DrupalApi\Client;
use Hussainweb\DrupalApi\Entity\TaxonomyTerm;
use Hussainweb\DrupalApi\Request\TaxonomyTermRequest;

class RetrieveTermJob extends RetrieveJobBase
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo "Retrieving " . (string) $this->request->getUri() . "\n";

        $client = new Client();
        /** @var TaxonomyTerm $term */
        $term = $client->getEntity($this->request);

        if (empty($term->tid)) {
            echo "Skipping empty term " . (string) $this->request->getUri() . "\n";
            return;
        }

        $this->saveDataToModel($term, new Term());
    }
}
