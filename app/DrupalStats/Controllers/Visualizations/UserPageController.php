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

    public function userExpertise()
    {
        return view('users/user-expertise', ['title' => 'User - Expertise']);
    }

    public function userCountries()
    {
        return view('users/user-countries', ['title' => 'User - Countries']);
    }

    public function userGrowth()
    {
        $values = [
            'title' => 'User Growth',
            'data_url' => url('data/user-growth'),
            'data_types' => [
                'total' => 'Total',
            ],
            'default_data_types' => [
                'total',
            ],
            'show_filter' => false,
            'y_number_format' => '2s',
        ];
        return view('users/user-growth', $values);
    }

    public function userGenderGrowth()
    {
        $values = [
            'title' => 'User Gender Growth',
            'data_url' => url('data/user-gender-growth'),
            'data_types' => [
                'na' => 'N/A',
                'male' => 'Male',
                'female' => 'Female',
                'other' => 'Other',
                'transgender' => 'Transgender',
            ],
            'default_data_types' => [
                'male',
                'female',
                'other',
                'transgender',
            ],
            'show_filter' => true,
            'y_number_format' => '2s',
        ];
        return view('users/user-gender-growth', $values);
    }

    public function userCountryGrowth()
    {
        $values = [
            'title' => 'User Country Growth',
            'data_url' => url('data/user-country-growth'),
            'data_types' => [],
            'default_data_types' => [],
            'show_filter' => false,
            'y_number_format' => '2s',
        ];
        return view('users/user-country-growth', $values);
    }
}
