<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

use App\DrupalStats\Models\Repositories\FieldOrganizationRepository;
use Hussainweb\DrupalApi\Client;
use Hussainweb\DrupalApi\Entity\FieldCollection;

class RetrieveFieldOrganizationJob extends RetrieveJobBase
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

        $repo = new FieldOrganizationRepository();
        $repo->saveEntity($fc);
    }
}
