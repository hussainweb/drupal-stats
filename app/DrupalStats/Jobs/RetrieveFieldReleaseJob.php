<?php

/**
 * @file
 */

namespace App\DrupalStats\Jobs;

use App\DrupalStats\Models\Entities\FieldCollectionRelease;
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

        $this->saveDataToModel($fc, new FieldCollectionRelease(), function ($key, $value) {
            if ($key == 'host_entity') {
                unset($value->uri);
            }
            elseif ($key == 'field_release_file') {
                unset($value->file->uri);
                unset($value->file->resource);
            }
            return $value;
        });
    }
}
