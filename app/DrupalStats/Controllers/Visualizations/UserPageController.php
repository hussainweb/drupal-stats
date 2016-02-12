<?php

/**
 * @file
 */

namespace App\DrupalStats\Controllers\Visualizations;

use App\Http\Controllers\Controller;

class UserPageController extends Controller
{

    public function userLanguages()
    {
        return view('users/user-languages', ['title' => 'User - Languages']);
    }
}
