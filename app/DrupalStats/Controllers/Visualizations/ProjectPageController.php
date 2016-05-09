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
        $values = [
            'title' => 'Projects Growth',
            'data_url' => url('data/projects-growth'),
            'data_types' => [
                'project_module' => 'Modules',
                'project_theme' => 'Themes',
                'project_distribution' => 'Distributions',
                'project_core' => 'Core',
                'project_theme_engine' => 'Theme Engines',
            ],
            'default_data_types' => [
              'project_module',
              'project_theme',
              'project_distribution',
              'project_core',
              'project_theme_engine',
            ],
        ];
        return view('projects/projects-growth', $values);
    }
}
