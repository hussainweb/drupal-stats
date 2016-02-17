<?php

/**
 * @file
 */

namespace App\DrupalStats\Controllers\Visualizations;

use App\Http\Controllers\Controller;

class ProjectIssuePageController extends Controller
{

    public function projectIssueBreakup($name = '')
    {
        $title = 'Issues Breakdown';
        if ($name) {
            $title = sprintf('Project - Issue Breakdown (%s)', $name);
        }
        return view('issues/project-breakdown', [
            'title' => $title,
            'project_name' => $name,
        ]);
    }
}
