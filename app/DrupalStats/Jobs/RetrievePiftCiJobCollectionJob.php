<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

use App\DrupalStats\Models\Repositories\PiftCiJobRepository;
use Hussainweb\DrupalApi\Client;
use Hussainweb\DrupalApi\Entity\PiftCiJob;
use Hussainweb\DrupalApi\Request\Collection\PiftCiJobCollectionRequest;

class RetrievePiftCiJobCollectionJob extends RetrieveJobBase
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
        $repo = new PiftCiJobRepository();

        /** @var PiftCiJob $term */
        foreach ($collection as $term) {
            $repo->saveEntity($term);
        }

        if ($next_url = $collection->getNextLink()) {
            $next_url_params = [];
            parse_str($next_url->getQuery(), $next_url_params);
            $this->dispatch(new RetrievePiftCiJobCollectionJob(new PiftCiJobCollectionRequest($next_url_params)));
        }
    }
}
