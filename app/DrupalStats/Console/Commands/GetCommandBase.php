<?php

/**
 * @file
 */

namespace App\DrupalStats\Console\Commands;

use Illuminate\Console\Command;

abstract class GetCommandBase extends Command
{

    public function __construct()
    {
        $this->signature .= ' {--page=0 : Page to begin retrieving}
                              {--sort= : Sort criteria}
                              {--direction= : Direction of sorting}';
        parent::__construct();
    }

    protected function getQueryFromOptions()
    {
        $query = [
            'page' => $this->option('page'),
        ];

        if ($opt = $this->option('sort')) {
            $query['sort'] = $opt;
        }
        if ($opt = $this->option('direction')) {
            $query['direction'] = $opt;
        }

        return $query;
    }
}
