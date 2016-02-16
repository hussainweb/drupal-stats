<?php

/**
 * @file
 */

namespace App\DrupalStats\Console\Commands;

use App\DrupalStats\Jobs\RetrievePiftCiJobCollectionJob;
use App\DrupalStats\Models\Services\JobStatusService;
use Hussainweb\DrupalApi\Request\Collection\PiftCiJobCollectionRequest;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\DB;
use MongoDB\Database;

class UpdatePiftCiJob extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dsupdate:cijobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize queue with commands to update ci jobs.';

    public function handle(JobStatusService $job_status_service)
    {
        $job = $job_status_service->getJobStatus('pift_ci_jobs', function () {
            /** @var Database $db */
            $db = DB::getMongoDB();

            $last_job = $db->pift_ci_jobs->findOne([], [
              'sort' => ['updated' => -1],
              'limit' => 1,
            ]);
            return $last_job->updated;
        });

        if (!empty($job->queued)) {
            $this->error('The request is already queued. You should see updates soon.');
            return 1;
        }

        $req = new PiftCiJobCollectionRequest([
          'sort' => 'updated',
          'direction' => 'DESC',
        ]);
        $options = [
          'last_updated' => $job->last_updated,
        ];
        $this->dispatch(new RetrievePiftCiJobCollectionJob($req, $options));

        $job->queued = true;
        $job->save();

        $this->line('The request is now queued. You should see updates soon.');
        return 0;
    }
}
