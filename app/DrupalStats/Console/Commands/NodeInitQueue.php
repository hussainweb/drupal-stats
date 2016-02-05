<?php

namespace App\DrupalStats\Console\Commands;

use App\DrupalStats\Jobs\RetrieveNodeCollectionJob;
use Hussainweb\DrupalApi\Request\Collection\NodeCollectionRequest;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class NodeInitQueue extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drustats:nodeinit {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize queue with node commands';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $params = [];
        if ($type = $this->argument('type')) {
            $params['type'] = $type;
        }
        $this->dispatch(new RetrieveNodeCollectionJob(new NodeCollectionRequest($params)));
    }
}
