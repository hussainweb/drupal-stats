<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

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

        /** @var Node $node */
        foreach ($collection as $node) {
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

        if ($next_url = $collection->getNextLink()) {
            $next_url_params = [];
            parse_str($next_url->getQuery(), $next_url_params);
            $this->dispatch(new RetrieveNodeCollectionJob(new NodeCollectionRequest($next_url_params), $this->options));
        }
    }
}
