<?php

/**
 * @file
 */

namespace App\DrupalStats\Controllers\Visualizations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

    public function projectIssueCount(Request $request)
    {
        $title = 'Issues Count in projects';
        $url = "data/project-issue-count";
        if ($request->input('open_issues')) {
            $url .= "?open_issues=1";
        }
        return view('issues/project-issue-count', [
            'title' => $title,
            'url' => url($url),
        ]);
    }
}
