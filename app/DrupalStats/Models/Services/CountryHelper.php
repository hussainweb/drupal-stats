<?php

/**
 * @file
 */

namespace App\DrupalStats\Models\Services;

class CountryHelper
{

    protected $countries;
    protected $countryCodes;

    public function __construct()
    {
        $this->countryCodes = json_decode(file_get_contents(resource_path('country-list.json')), true);
        $this->countries = array_flip($this->countryCodes);
    }

    public function getCountry($alpha3_code)
    {
        return isset($this->countries[$alpha3_code]) ? $this->countries[$alpha3_code] : null;
    }

    public function getCountryCode($name)
    {
        return isset($this->countryCodes[$name]) ? $this->countryCodes[$name] : null;
    }
}
