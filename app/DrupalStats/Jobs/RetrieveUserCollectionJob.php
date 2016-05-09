<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

use App\DrupalStats\Models\Entities\JobStatus;
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

        // Save the maximum updated value in options as we won't have the actual
        // maximum when parsing later pages.
        $max_uid = $this->getOption('max_uid');
        $hit_last_uid = false;

        /** @var User $user */
        foreach ($collection as $user) {
            // Skip anonymous user as we don't need to save it.
            if (!$user->uid) {
                continue;
            }
            $max_uid = ($max_uid < $user->uid) ? $user->uid : $max_uid;
            if (!empty($this->options['last_uid']) && $user->uid < $this->options['last_uid']) {
                $hit_last_uid = true;
                break;
            }
            $repo->saveEntity($user);
        }

        foreach ($repo->organizations as $organization) {
            echo "Queuing organization " . $organization . "...\n";
            $this->dispatch(new RetrieveFieldOrganizationJob(new FieldCollectionRequest($organization)));
        }

        if (!$hit_last_uid && $next_url = $collection->getNextLink()) {
            $next_url_params = [];
            parse_str($next_url->getQuery(), $next_url_params);
            $this->options['max_uid'] = $max_uid;
            $this->dispatch(new RetrieveUserCollectionJob(new UserCollectionRequest($next_url_params), $this->options));
        }
        else {
            if (!empty($this->options['last_uid']) && $job_status = JobStatus::find('users')) {
                echo sprintf("Completed retrieving users from uid %d.\n",
                  $this->options['last_uid']);
                $job_status->queued = false;
                $job_status->last_uid = $max_uid;
                $job_status->save();
            }
        }
    }
}
