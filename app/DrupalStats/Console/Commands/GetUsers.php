<?php

/**
 * @file
 */

namespace App\DrupalStats\Console\Commands;

use App\DrupalStats\Jobs\RetrieveUserCollectionJob;
use Hussainweb\DrupalApi\Request\Collection\UserCollectionRequest;
use Illuminate\Foundation\Bus\DispatchesJobs;

class GetUsers extends GetCommandBase
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'druget:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize queue with commands to get users.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->dispatch(new RetrieveUserCollectionJob(new UserCollectionRequest($this->getQueryFromOptions())));
    }
}
