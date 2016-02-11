<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

use App\DrupalStats\Models\Entities\JobStatus;
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

        $max_last_updated = null;
        $hit_last_updated = false;

        /** @var PiftCiJob $job */
        foreach ($collection as $job) {
            $max_last_updated = ($max_last_updated < $job->updated) ? $job->updated : $max_last_updated;
            if (!empty($this->options['last_updated']) && $job->updated < $this->options['last_updated']) {
                $hit_last_updated = true;
                break;
            }
            $repo->saveEntity($job);
        }

        if (!$hit_last_updated && $next_url = $collection->getNextLink()) {
            $next_url_params = [];
            parse_str($next_url->getQuery(), $next_url_params);
            $this->dispatch(new RetrievePiftCiJobCollectionJob(new PiftCiJobCollectionRequest($next_url_params), $this->options));
        }
        else {
            if (!empty($this->options['last_updated']) && $job_status = JobStatus::find('pift_ci_jobs')) {
                echo sprintf("Completed retrieving ci jobs from %s.\n",
                  date('Y-m-d H:i:s', $this->options['last_updated']));
                $job_status->queued = false;
                $job_status->last_updated = $max_last_updated;
                $job_status->save();
            }
        }
    }
}
