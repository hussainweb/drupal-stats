<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

use App\Jobs\Job;
use Hussainweb\DrupalApi\Entity\Entity;
use Hussainweb\DrupalApi\Request\Request;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Jenssegers\Mongodb\Eloquent\Model;

abstract class RetrieveJobBase extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    use DispatchesJobs;

    /**
     * @var \Hussainweb\DrupalApi\Request\Request
     */
    protected $request;

    /**
     * Additional options relevant to the job.
     *
     * @var array
     */
    protected $options;

    public function __construct(Request $request, array $options = [])
    {
        $this->request = $request;
        $this->options = $options;
    }
}
