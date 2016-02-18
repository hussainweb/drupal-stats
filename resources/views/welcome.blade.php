@extends('layouts.master')

@section('content')
    <div class="content">
        <div class="title">Drupal Statistics Collector</div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <a href="{{ url('viz/module-downloads') }}">
                <img src="{{ asset('images/01-module-downloads.png') }}" alt="Module Downloads Bubble">
            </a>
            <h3>Module Downloads Bubble</h3>
            <p>The top 200 modules from drupal.org arranged by downloads and category.</p>
            <a href="{{ url('viz/module-downloads') }}" class="btn btn-info">View</a>
        </div>
        <div class="col-sm-4">
            <a href="{{ url('viz/project-downloads') }}">
                <img src="{{ asset('images/02-project-downloads.png') }}" alt="Project Downloads Bubble">
            </a>
            <h3>Project Downloads Bubble</h3>
            <p>The top 200 projects from drupal.org arranged by downloads and project type.</p>
            <a href="{{ url('viz/project-downloads') }}" class="btn btn-info">View</a>
        </div>
        <div class="col-sm-4">
            <a href="{{ url('viz/projects-growth') }}">
                <img src="{{ asset('images/03-projects-growth.png') }}" alt="Project Growth">
            </a>
            <h3>Project Growth</h3>
            <p>The growth of each project type over the years.</p>
            <a href="{{ url('viz/projects-growth') }}" class="btn btn-info">View</a>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <a href="{{ url('viz/issues') }}">
                <img src="{{ asset('images/04-issues-breakdown.png') }}" alt="Issues Breakdown">
            </a>
            <h3>Issues Breakdown</h3>
            <p>Breakdown of issues for all projects or for a specific project.</p>
            <a href="{{ url('viz/issues') }}" class="btn btn-info">View</a>
        </div>
        <div class="col-sm-4">
            <a href="{{ url('viz/user-countries') }}">
                <img src="{{ asset('images/05-user-countries.png') }}" alt="User Countries">
            </a>
            <h3>User Countries</h3>
            <p>User registrations on drupal.org per each country in the world.</p>
            <a href="{{ url('viz/user-countries') }}" class="btn btn-info">View</a>
        </div>
        <div class="col-sm-4">
            <a href="{{ url('viz/user-languages') }}">
                <img src="{{ asset('images/06-user-languages.png') }}" alt="User Languages">
            </a>
            <h3>User Languages</h3>
            <p>Languages spoken by drupal.org users</p>
            <a href="{{ url('viz/user-languages') }}" class="btn btn-info">View</a>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <a href="{{ url('viz/user-expertise') }}">
                <img src="{{ asset('images/07-user-expertise.png') }}" alt="User Expertise">
            </a>
            <h3>User Expertise</h3>
            <p>Areas of expertise of drupal.org users</p>
            <a href="{{ url('viz/user-expertise') }}" class="btn btn-info">View</a>
        </div>
        <div class="col-sm-4">
            <a href="{{ url('viz/ci-jobs') }}">
                <img src="{{ asset('images/08-tests-status.png') }}" alt="CI Jobs Status">
            </a>
            <h3>CI Jobs Status</h3>
            <p>Status of CI jobs running on each branch</p>
            <a href="{{ url('viz/ci-jobs') }}" class="btn btn-info">View</a>
        </div>
        <div class="col-sm-4">
            <a href="{{ url('viz/ci-jobs-reasons') }}">
                <img src="{{ asset('images/09-tests-reasons.png') }}" alt="CI Jobs Reasons">
            </a>
            <h3>CI Jobs Reasons</h3>
            <p>Reasons for each CI job run</p>
            <a href="{{ url('viz/ci-jobs-reasons') }}" class="btn btn-info">View</a>
        </div>
    </div>
@endsection
