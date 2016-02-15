<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

use App\DrupalStats\Models\Entities\JobStatus;
use App\DrupalStats\Models\Entities\Term;
use App\DrupalStats\Models\Repositories\NodeRepository;
use Hussainweb\DrupalApi\Client;
use Hussainweb\DrupalApi\Entity\Node;
use Hussainweb\DrupalApi\Request\Collection\NodeCollectionRequest;
use Hussainweb\DrupalApi\Request\FieldCollectionRequest;
use Hussainweb\DrupalApi\Request\TaxonomyTermRequest;

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
        $repo = new NodeRepository();

        // Save the maximum updated value in options as we won't have the actual
        // maximum when parsing later pages.
        $max_updated = $this->getMaxUpdated();
        $hit_last_updated = false;

        /** @var Node $node */
        foreach ($collection as $node) {
            $max_updated = ($max_updated < $node->changed) ? $node->changed : $max_updated;
            if (!empty($this->options['last_updated']) && $node->changed < $this->options['last_updated']) {
                $hit_last_updated = true;
                break;
            }
            $repo->saveEntity($node);
        }

        foreach ($repo->terms as $tid) {
            if (is_null(Term::find($tid))) {
                echo "Queueing term " . $tid . "...\n";
                $this->dispatch(new RetrieveTermJob(new TaxonomyTermRequest($tid)));
            }
        }

        foreach ($repo->releases as $release) {
            echo "Queuing release " . $release . "...\n";
            $this->dispatch(new RetrieveFieldReleaseJob(new FieldCollectionRequest($release)));
        }

        if (!$hit_last_updated && $next_url = $collection->getNextLink()) {
            $next_url_params = [];
            parse_str($next_url->getQuery(), $next_url_params);
            $this->options['max_updated'] = $max_updated;
            $this->dispatch(new RetrieveNodeCollectionJob(new NodeCollectionRequest($next_url_params), $this->options));
        }
        else {
            if (!empty($this->options['last_updated']) && $job_status = JobStatus::find('nodes-' . $this->getOption('type', ''))) {
                echo sprintf("Completed retrieving nodes from %s.\n",
                  date('Y-m-d H:i:s', $this->options['last_updated']));
                $job_status->queued = false;
                $job_status->last_updated = $max_updated;
                $job_status->save();
            }
        }
    }
}
