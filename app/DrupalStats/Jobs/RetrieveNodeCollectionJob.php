<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

use App\DrupalStats\Models\Entities\Node as NodeModel;
use App\Jobs\Job;
use Hussainweb\DrupalApi\Client;
use Hussainweb\DrupalApi\Entity\Node;
use Hussainweb\DrupalApi\Request\Collection\NodeCollectionRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RetrieveNodeCollectionJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    use DispatchesJobs;

    /**
     * @var \Hussainweb\DrupalApi\Request\Collection\NodeCollectionRequest
     */
    protected $collectionRequest;

    public function __construct(NodeCollectionRequest $collection_request)
    {
        $this->collectionRequest = $collection_request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client();
        $collection = $client->getEntity($this->collectionRequest);
        /** @var Node $item */
        foreach ($collection as $item) {
            $model = new NodeModel();
            $model->_id = $item->getId();
            foreach ($item->getData() as $key => $value) {
                $model->$key = $value;
            }
            $model->save();
        }

        if ($next_url = $collection->getNextLink()) {
            $next_url_params = [];
            parse_str($next_url->getQuery(), $next_url_params);
            $job = new NodeCollectionRequest($next_url_params);
            $this->dispatch(new RetrieveNodeCollectionJob($job));
        }
    }
}
