<?php

/**
 * @file
 */

namespace App\DrupalStats\Controllers\Visualizations;

use App\Http\Controllers\Controller;

class ProjectPageController extends Controller
{

    public function moduleDownloads()
    {
        return view('module-downloads', ['title' => 'Module - Categories - Downloads Bubble Chart']);
    }
}
