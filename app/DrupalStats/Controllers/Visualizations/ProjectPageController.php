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
        return view('projects/module-downloads', ['title' => 'Module - Categories - Downloads Bubble Chart']);
    }

    public function projectDownloads()
    {
        return view('projects/project-downloads', ['title' => 'Projects - Downloads Bubble Chart']);
    }

    public function projectsGrowth()
    {
        return view('projects/projects-growth', ['title' => 'Projects Growth']);
    }
}
