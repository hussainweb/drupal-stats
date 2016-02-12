<?php

/**
 * @file
 */

namespace App\DrupalStats\Controllers\Visualizations;

use App\Http\Controllers\Controller;

class CiJobPageController extends Controller
{

    public function cijobStatus()
    {
        return view('ci-jobs/test-jobs', ['title' => 'CI Jobs - Status of tests']);
    }

    public function cijobReason()
    {
        return view('ci-jobs/test-reasons', ['title' => 'CI Jobs - Reasons for testing']);
    }
}
