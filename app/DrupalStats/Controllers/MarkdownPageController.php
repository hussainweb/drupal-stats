<?php

/**
 * @file
 */

namespace App\DrupalStats\Controllers;

use App\Http\Controllers\Controller;
use Michelf\MarkdownExtra;

class MarkdownPageController extends Controller
{

    public function aboutPage()
    {
        $title = 'About this site';
        $content = MarkdownExtra::defaultTransform(file_get_contents(base_path('about.md')));
        return view('simple-content', [
            'title' => $title,
            'page_content' => $content,
        ]);
    }
}
