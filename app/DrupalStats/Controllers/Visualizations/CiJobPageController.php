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
        return view('test-jobs', ['title' => 'CI Jobs']);
    }
}
