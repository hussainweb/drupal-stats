<?php

/**
 * @file
 */

namespace App\DrupalStats\Console\Commands;

use App\DrupalStats\Jobs\RetrieveNodeCollectionJob;
use App\DrupalStats\Models\Services\JobStatusService;
use Hussainweb\DrupalApi\Request\Collection\NodeCollectionRequest;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\DB;
use MongoDB\Database;

class UpdateNodes extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dsupdate:nodes {type : Node type to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize queue with commands to update nodes.';

    public function handle(JobStatusService $job_status_service)
    {
        $type = $this->argument('type');
        $job = $job_status_service->getJobStatus('nodes-' . $type, function () use ($type) {
            /** @var Database $db */
            $db = DB::getMongoDB();

            $last_job = $db->nodes->findOne([], [
                'sort' => ['changed' => -1],
                'type' => $type,
                'limit' => 1,
            ]);
            return $last_job->changed;
        });

        if (!empty($job->queued)) {
            $this->error('The request is already queued. You should see updates soon.');
            return 1;
        }

        $req = new NodeCollectionRequest([
            'type' => $type,
            'sort' => 'changed',
            'direction' => 'DESC',
        ]);
        $options = [
            'last_updated' => $job->last_updated,
            'type' => $type,
        ];
        $this->dispatch(new RetrieveNodeCollectionJob($req, $options));

        $job->queued = true;
        $job->save();

        $this->line('The request is now queued. You should see updates soon.');
        return 0;
    }
}
