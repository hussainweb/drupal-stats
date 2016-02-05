<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

use App\DrupalStats\Models\Entities\FieldCollectionRelease;
use App\DrupalStats\Models\Repositories\FieldReleaseRepository;
use Hussainweb\DrupalApi\Client;
use Hussainweb\DrupalApi\Entity\FieldCollection;

class RetrieveFieldReleaseJob extends RetrieveJobBase
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo "Retrieving " . (string) $this->request->getUri() . "\n";

        $client = new Client();
        /** @var FieldCollection $term */
        $fc = $client->getEntity($this->request);

        if (empty($fc->item_id)) {
            echo "Skipping empty field collection " . (string) $this->request->getUri() . "\n";
            return;
        }

        $repo = new FieldReleaseRepository();
        $repo->saveEntity($fc);
    }
}
