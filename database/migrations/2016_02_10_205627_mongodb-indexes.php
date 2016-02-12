<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MongodbIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nodes', function ($collection) {
            $collection->index('type');
            $collection->index('title');
            $collection->index('changed');
            $collection->index('comment_count');
            $collection->index('author.id');
            $collection->index('field_download_count');
            $collection->index('field_project_type');
            $collection->index('field_release_project.id');
            $collection->index('field_supporting_organizations.id');
            $collection->index('taxonomy_vocabulary_3.id');
            $collection->index('taxonomy_vocabulary_6.id');
            $collection->index('taxonomy_vocabulary_7.id');
            $collection->index('taxonomy_vocabulary_44.id');
            $collection->index('taxonomy_vocabulary_46.id');
            $collection->index('taxonomy_vocabulary_52.id');
        });

        Schema::table('users', function ($collection) {
            $collection->index('field_areas_of_expertise.id');
            $collection->index('field_country');
            $collection->index('field_gender');
            $collection->index('field_languages');
            $collection->index('field_organizations.id');
        });

        Schema::table('field_collection_organizations', function ($collection) {
            $collection->index('host_entity.id');
            $collection->index('field_organization_name');
        });

        Schema::table('field_collection_releases', function ($collection) {
            $collection->index('host_entity.id');
            $collection->index('field_release_file_downloads');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
