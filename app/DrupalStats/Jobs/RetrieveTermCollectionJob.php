<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

use App\DrupalStats\Models\Repositories\TermRepository;
use Hussainweb\DrupalApi\Client;
use Hussainweb\DrupalApi\Entity\TaxonomyTerm;
use Hussainweb\DrupalApi\Request\Collection\TaxonomyTermCollectionRequest;

class RetrieveTermCollectionJob extends RetrieveJobBase
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
        $repo = new TermRepository();

        /** @var TaxonomyTerm $term */
        foreach ($collection as $term) {
            $repo->saveEntity($term);
        }

        if ($next_url = $collection->getNextLink()) {
            $next_url_params = [];
            parse_str($next_url->getQuery(), $next_url_params);
            $this->dispatch(new RetrieveTermCollectionJob(new TaxonomyTermCollectionRequest($next_url_params), $this->options));
        }
    }
}
