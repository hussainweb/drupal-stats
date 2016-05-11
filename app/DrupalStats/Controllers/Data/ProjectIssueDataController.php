<?php

/**
 * @file
 */

namespace App\DrupalStats\Controllers\Data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MongoDB\Database;

class ProjectIssueDataController extends Controller
{

    protected static $issuePriorities = [
        400 => 'Critical',
        300 => 'Major',
        200 => 'Normal',
        100 => 'Minor',
    ];

    protected static $issueStatus = [
        1 => 'active',
        2 => 'fixed',
        3 => 'closed (duplicate)',
        4 => 'postponed',
        5 => 'closed (won\'t fix)',
        6 => 'closed (works as designed)',
        7 => 'closed (fixed)',
        8 => 'needs review',
        13 => 'needs work',
        14 => 'reviewed & tested by the community',
        15 => 'patch (to be ported)',
        16 => 'postponed (maintainer needs more info)',
        18 => 'closed (cannot reproduce)',
    ];

    protected static $openIssueStatus = [
        1,
        2,
        4,
        8,
        13,
        14,
        15,
        16,
    ];

    protected static $issueCategories = [
        1 => 'Bug report',
        2 => 'Task',
        3 => 'Feature request',
        4 => 'Support request',
        5 => 'Plan',
    ];

    public function __construct()
    {
        $this->middleware('data.cache');
    }

    public function projectIssueBreakup($name = '')
    {
        /** @var Database $db */
        $db = DB::getMongoDB();

        $match = [
            'type' => 'project_issue',
        ];

        if ($name) {
            $project = $db->nodes->findOne([
                'field_project_machine_name' => $name,
            ]);
            if (!$project) {
                return response()->json([
                    'error' => 'The specified project was not found',
                ])->setStatusCode(404);
            }

            $match['field_project.id'] = $project->_id;
        }

        $issues = $db->nodes->aggregate([
            [
                '$match' => $match,
            ],
            [
                '$group' => [
                    '_id' => [
                        'issue_priority' => '$field_issue_priority',
                        'issue_status' => '$field_issue_status',
                        'issue_category' => '$field_issue_category',
                    ],
                    'count' => ['$sum' => 1],
                ],
            ],
            [
                // This sort order is important. It allows us to construct all
                // data with just one loop.
                '$sort' => ['_id.issue_category' => 1, '_id.issue_priority' => 1, '_id.issue_status' => 1],
            ],
        ])->toArray();

        $categories = $priorities = $statuses = [];
        $total = 0;
        foreach ($issues as $issue) {
            $category = array_key_exists($issue->_id->issue_category, static::$issueCategories) ? static::$issueCategories[$issue->_id->issue_category] : '';
            $priority = array_key_exists($issue->_id->issue_priority, static::$issuePriorities) ? static::$issuePriorities[$issue->_id->issue_priority] : '';
            $status = array_key_exists($issue->_id->issue_status, static::$issueStatus) ? static::$issueStatus[$issue->_id->issue_status] : '';
            if (!$category) {
                continue;
            }

            if (!isset($categories[$category])) {
                $categories[$category] = [
                    'text' => $category,
                    'fullText' => $category,
                    'count' => 0,
                ];
            }

            $categories[$category]['count'] += $issue->count;

            if (!isset($priorities[$category . ':' . $priority])) {
                $priorities[$category . ':' . $priority] = [
                  'text' => $priority,
                  'fullText' => $category . ': ' . $priority,
                  'count' => 0,
                ];
            }
            $priorities[$category . ':' . $priority]['count'] += $issue->count;

            if (!isset($statuses[$category . ':' . $priority . ':' . $status])) {
                $statuses[$category . ':' . $priority . ':' . $status] = [
                  'text' => $status,
                  'fullText' => $category . ': ' . $priority . ': ' . $status,
                  'count' => 0,
                ];
            }
            $statuses[$category . ':' . $priority . ':' . $status]['count'] += $issue->count;

            $total += $issue->count;
        }

        return response()->json([
            'category' => array_values($categories),
            'priority' => array_values($priorities),
            'status' => array_values($statuses),
            'totalIssues' => $total,
        ]);
    }

    public function projectIssueCount(Request $request)
    {
        /** @var Database $db */
        $db = DB::getMongoDB();

        $match = [
            'type' => 'project_issue',
        ];

        if ($request->input('open_issues')) {
            $match['field_issue_status'] = [
              '$in' => array_map(function ($value) {
                  return (string) $value;
              }, static::$openIssueStatus),
            ];
        }

        $issues = $db->nodes->aggregate([
            [
                '$match' => $match,
            ],
            [
                '$group' => [
                    '_id' => [
                        'project' => '$field_project.id',
                    ],
                    'count' => ['$sum' => 1],
                ],
            ],
            [
                '$sort' => ['count' => -1],
            ],
            [
                '$limit' => 200,
            ],
        ])->toArray();

        $ret = [];
        foreach ($issues as $issue) {
            $project = $db->nodes->findOne(['_id' => $issue->_id->project]);
            if ($project) {
                $ret[] = [
                  '_id' => $project['nid'],
                  'title' => $project['title'],
                  'machine_name' => $project['field_project_machine_name'],
                  'type' => $project['type'],
                  'value' => $issue->count,
                ];
            }
        }

        return response()->json($ret);
    }
}
