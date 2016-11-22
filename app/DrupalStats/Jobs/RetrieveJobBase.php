<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

use Hussainweb\DrupalApi\Entity\Entity;
use Hussainweb\DrupalApi\Request\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class RetrieveJobBase implements ShouldQueue
{
    use Queueable;

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

    protected function getOption($name, $default = null)
    {
        return !empty($this->options[$name]) ? $this->options[$name] : $default;
    }

    protected function getMaxUpdated()
    {
        return $this->getOption('max_updated');
    }
}
