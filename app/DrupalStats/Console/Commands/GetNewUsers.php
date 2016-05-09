<?php

/**
 * @file
 */

namespace App\DrupalStats\Console\Commands;

use App\DrupalStats\Jobs\RetrieveUserCollectionJob;
use App\DrupalStats\Models\Services\JobStatusService;
use Hussainweb\DrupalApi\Request\Collection\UserCollectionRequest;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\DB;
use MongoDB\Database;

class GetNewUsers extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dsget:new-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize queue with commands to retrieve new users.';

    public function handle(JobStatusService $job_status_service)
    {
        $job = $job_status_service->getJobStatus('users', function () {
            /** @var Database $db */
            $db = DB::getMongoDB();

            $last_job = $db->users->findOne([], [
              'sort' => ['uid' => -1],
              'limit' => 1,
            ]);
            return $last_job->uid;
        });

        if (!empty($job->queued)) {
            $this->error('The request is already queued. You should see updates soon.');
            return 1;
        }

        $req = new UserCollectionRequest([
          'sort' => 'uid',
          'direction' => 'DESC',
        ]);
        $options = [
          'last_uid' => $job->last_uid,
        ];
        $this->dispatch(new RetrieveUserCollectionJob($req, $options));

        $job->queued = true;
        $job->save();

        $this->line('The request is now queued. You should see updates soon.');
        return 0;
    }
}
