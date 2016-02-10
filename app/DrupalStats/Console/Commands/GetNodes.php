<?php

/**
 * @file
 */

namespace App\DrupalStats\Console\Commands;

use App\DrupalStats\Jobs\RetrieveNodeCollectionJob;
use Hussainweb\DrupalApi\Request\Collection\NodeCollectionRequest;
use Illuminate\Foundation\Bus\DispatchesJobs;

class GetNodes extends GetCommandBase
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'druget:nodes {type : Node type to retrieve}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize queue with commands to get nodes.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $params = $this->getQueryFromOptions();
        if ($type = $this->argument('type')) {
            $params['type'] = $type;
        }
        $this->dispatch(new RetrieveNodeCollectionJob(new NodeCollectionRequest($params)));
    }
}
