# DruStats

[![Software License](https://img.shields.io/badge/license-GPLv2-brightgreen.svg?style=flat-square)](LICENSE.md)

DruStats provides visualizations for a variety of data obtained using the drupal.org API. This was built for a developer contest organized by Azri Solutions at DrupalCon Asia 2016. As per the contest rules, only data from drupal.org API is used to build the visualizations.

## Usage

See installation section for detailed instructions on installing this application.

Once installed, the website may be used as any other web application directly from the browser. With a clean database, the software will not be able to show any visualizations. PHP CLI (with Laravel Artisan) is used to initiate commands that queue requests for retrieving data.

These are the commands provided by this application to queue requests.

~~~sh
php artisan dsget:nodes {type}
php artisan dsget:cijobs
php artisan dsget:users
php artisan dsget:terms
php artisan dsupdate:nodes {type}
~~~

All the `dsget` commands have the following options.

~~~sh
--page={page} --sort={field} --direction={ASC|DESC}
~~~

All `dsget` commands start at the specified page (defaults to 0) and continue accessing the subsequent pages until it reaches the end. This is only required for initial setup or if you want to reset all data in the database. Normally, you would use `dsupdate` commands to retrieve fresh content.

## Requirements

The application needs the following software to run.

* PHP 5.5.9 (tested only on PHP 7)
* MongoDB 3.2
* PHP MongoDB extension
* Beanstalkd (the application would work without this but would not be able to update the database)
* Any web server that can work with PHP

To use the application, the following software is needed.

* Any modern browser (IE9+, Chrome, Firefox)
* Javascript to view the visualizations

### Development Requirements

To develop with this application, the following software is needed in addition to the above requirements.

* Composer
* Node.js (with npm)
* Gulp

## Installation

These are the software components and libraries required to use DruStats.

* Apache, nginx, or any webserver compatible with PHP-FPM or PHP modules
* [PHP 5.5.9](http://php.net/downloads.php) (PHP 7 recommended)
* [MongoDB 3.2](https://docs.mongodb.org/manual/installation/)
* [PHP MongoDB](http://php.net/manual/en/mongodb.setup.php) extension
* [Beanstalk](http://kr.github.io/beanstalkd/download.html)
* [Composer](https://getcomposer.org/) (installed globally)

Clone [this repository](https://github.com/hussainweb/drupal-stats) and run the following command in the directory.

~~~sh
$ git clone https://github.com/hussainweb/drupal-stats.git
$ cd drupal-stats/
$ composer install
~~~

Once composer installs the framework and other required libraries, run the migrations to setup the database. Currently, only indexes are created and the migrations are not strictily necessary for the working but it is recommended to migrate anyway.

~~~sh
$ php artisan migrate
~~~

The application is now ready for use and only needs data. Refer to the usage section to see how to start retrieving data from d.o. It might be a good idea to start with a database dump rather than retrieving the whole data yourself. Contact me for a database dump.

## License

This application is open-sourced software licensed under the [GPLv2 license](http://opensource.org/licenses/GPL-2.0).

### Third Party Components

This application is built using Laravel and many languages and technologies. It also uses data from various sources and the licenses are mentioned on a best-effort basis. All these licenses permit usage of these libraries or data for the purpose of this application and the license is preserved.

* Laravel - [MIT License](http://opensource.org/licenses/MIT)
* MongoDB - [GNU AGPL v3.0](http://www.fsf.org/licensing/licenses/agpl-3.0.html)
* PHP MongoDB extension - [Apache License](http://www.apache.org/licenses/LICENSE-2.0)
* Beanstalk - [MIT](https://github.com/kr/beanstalkd/blob/master/LICENSE)
* Drupal API Client - [GPL v2](https://github.com/hussainweb/drupal-api-client/blob/master/LICENSE.md)
* Laravel MongoDB model - [MIT](https://github.com/jenssegers/laravel-mongodb/blob/master/composer.json)
* Pheanstalk - [MIT](https://github.com/pda/pheanstalk/blob/master/LICENSE)
* Artisan Beans - [MIT](https://github.com/pmatseykanets/artisan-beans/blob/master/LICENSE.txt)
* Bootstrap - [MIT](https://github.com/twbs/bootstrap/blob/master/LICENSE)
* d3.js - [BSD License](https://github.com/mbostock/d3/blob/master/LICENSE)
* d3-legend - [Permissive License](https://github.com/susielu/d3-legend/blob/master/LICENSE)
* d3-cloud - [BSD](https://github.com/jasondavies/d3-cloud/blob/master/LICENSE)
* Johan World GeoJSON - [UNLICENSE](https://github.com/johan/world.geo.json/blob/master/UNLICENSE)
