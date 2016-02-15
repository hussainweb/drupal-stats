<?php

/**
 * @file
 */

namespace App\DrupalStats\Models\Services;

use App\DrupalStats\Models\Entities\JobStatus;

class JobStatusService
{

    public function getJobStatus($name, callable $get_last_updated)
    {
        $job = JobStatus::find($name);

        if (!$job) {
            $job = new JobStatus();
            $job->_id = $name;
            $job->last_updated = $get_last_updated();
            $job->queued = false;
        }

        return $job;
    }
}
