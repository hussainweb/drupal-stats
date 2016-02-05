<?php

/**
 * @file
 */

namespace App\DrupalStats\Console\Commands;

use App\DrupalStats\Jobs\RetrieveTermCollectionJob;
use Hussainweb\DrupalApi\Request\Collection\TaxonomyTermCollectionRequest;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class GetTerms extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'druget:terms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize queue with commands to get terms.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->dispatch(new RetrieveTermCollectionJob(new TaxonomyTermCollectionRequest()));
    }
}
