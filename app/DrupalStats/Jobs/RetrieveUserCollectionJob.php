<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

use App\DrupalStats\Models\Entities\User as UserModel;
use Hussainweb\DrupalApi\Client;
use Hussainweb\DrupalApi\Entity\User;
use Hussainweb\DrupalApi\Request\Collection\UserCollectionRequest;

class RetrieveUserCollectionJob extends RetrieveJobBase
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

        /** @var User $item */
        foreach ($collection as $entity) {
            $this->saveDataToModel($entity, new UserModel());
        }

        if ($next_url = $collection->getNextLink()) {
            $next_url_params = [];
            parse_str($next_url->getQuery(), $next_url_params);
            $job = new UserCollectionRequest($next_url_params);
            $this->dispatch(new RetrieveUserCollectionJob($job));
        }
    }
}
