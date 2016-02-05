<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;


use App\DrupalStats\Models\Entities\Term;
use App\Jobs\Job;
use Hussainweb\DrupalApi\Client;
use Hussainweb\DrupalApi\Entity\TaxonomyTerm;
use Hussainweb\DrupalApi\Request\TaxonomyTermRequest;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RetrieveTermJob extends Job
{
    use InteractsWithQueue, SerializesModels;
    use DispatchesJobs;

    /**
     * @var \Hussainweb\DrupalApi\Request\TaxonomyTermRequest
     */
    protected $request;

    public function __construct(TaxonomyTermRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client();
        /** @var TaxonomyTerm $term */
        $term = $client->getEntity($this->request);

        if (empty($term->getData()->tid)) {
            return;
        }

        $model = Term::findOrNew($term->getId());
        $model->_id = $term->getId();
        foreach ($term->getData() as $key => $value) {
            $model->$key = $value;
        }
        $model->save();
    }
}
