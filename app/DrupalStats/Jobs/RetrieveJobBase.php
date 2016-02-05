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

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function saveDataToModel(Entity $entity, Model $model, callable $callback = null)
    {
        /** @var Model $item */
        $item = $model->findOrNew($entity->getId());
        $item->_id = $entity->getId();
        foreach ($entity->getData() as $key => $value) {
            if ($callback) {
                $value = call_user_func($callback, $key, $value);
            }
            $item->$key = $value;
        }
        $item->save();
    }
}
