<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

use App\DrupalStats\Models\Repositories\UserRepository;
use Hussainweb\DrupalApi\Client;
use Hussainweb\DrupalApi\Entity\User;
use Hussainweb\DrupalApi\Request\Collection\UserCollectionRequest;
use Hussainweb\DrupalApi\Request\FieldCollectionRequest;

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
        $repo = new UserRepository();

        /** @var User $user */
        foreach ($collection as $user) {
            // Skip anonymous user as we don't need to save it.
            if ($user->uid) {
                $repo->saveEntity($user);
            }
        }

        foreach ($repo->organizations as $organization) {
            echo "Queuing organization " . $organization . "...\n";
            $this->dispatch(new RetrieveFieldOrganizationJob(new FieldCollectionRequest($organization)));
        }

        if ($next_url = $collection->getNextLink()) {
            $next_url_params = [];
            parse_str($next_url->getQuery(), $next_url_params);
            $this->dispatch(new RetrieveUserCollectionJob(new UserCollectionRequest($next_url_params)));
        }
    }
}
