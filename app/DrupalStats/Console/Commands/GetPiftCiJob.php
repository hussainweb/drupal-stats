<?php

/**
 * @file
 */

namespace App\DrupalStats\Console\Commands;

use App\DrupalStats\Jobs\RetrievePiftCiJobCollectionJob;
use Hussainweb\DrupalApi\Request\Collection\PiftCiJobCollectionRequest;
use Illuminate\Foundation\Bus\DispatchesJobs;

class GetPiftCiJob extends GetCommandBase
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dsget:cijobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize queue with commands to get PIFT CI jobs.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->dispatch(new RetrievePiftCiJobCollectionJob(new PiftCiJobCollectionRequest($this->getQueryFromOptions())));
    }
}
