<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->options('(:any)', static function () {
    return service('response')->setStatusCode(204);
});

$routes->group('auth', function ($routes) {
    $routes->post('register', 'Api\V1\AuthController::register');
    $routes->post('login', 'Api\V1\AuthController::login');
});

$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->post('users', 'Api\V1\UserController::create');
    $routes->get('users', 'Api\V1\UserController::index');
    $routes->get('users/(:segment)', 'Api\V1\UserController::show/$1');
    $routes->delete('users/(:segment)', 'Api\V1\UserController::delete/$1');
    $routes->put('users/(:segment)', 'Api\V1\UserController::update/$1');
});

$routes->group('api', function ($routes) {
    $routes->post('auth/register', 'Api\V1\AuthController::register');
    $routes->post('auth/login', 'Api\V1\AuthController::login');

    $routes->get('locations', 'Api\V1\LocationController::index');
    $routes->get('locations/districts', 'Api\V1\LocationController::districts');
    $routes->get('search', 'Api\V1\SearchController::index');

    $routes->get('admissions/requirements', 'Api\V1\AdmissionController::requirements');
    $routes->post('admissions', 'Api\V1\AdmissionController::create');

    $routes->get('requirements', 'Api\V1\RequirementController::index');
    $routes->get('requirements/settings', 'Api\V1\RequirementController::settings');

    $routes->get('projects', 'Api\V1\ProjectController::index');
    $routes->get('projects/settings', 'Api\V1\ProjectController::settings');

    $routes->get('events', 'Api\V1\EventController::index');

    $routes->get('announcements', 'Api\V1\AnnouncementController::index');

    $routes->group('', ['filter' => 'admin'], function ($routes) {
        $routes->get('admin/users', 'Api\V1\UserController::index');
        $routes->post('admin/users', 'Api\V1\UserController::create');
        $routes->get('admin/users/(:segment)', 'Api\V1\UserController::show/$1');
        $routes->delete('admin/users/(:segment)', 'Api\V1\UserController::delete/$1');
        $routes->put('admin/users/(:segment)', 'Api\V1\UserController::update/$1');
        $routes->post('admin/users/update', 'Api\V1\UserController::update');

        $routes->get('admissions/requirements-all', 'Api\V1\AdmissionController::requirementsAll');
        $routes->post('admissions/requirements', 'Api\V1\AdmissionController::saveRequirements');
        $routes->get('admissions', 'Api\V1\AdmissionController::index');
        $routes->post('admissions/update', 'Api\V1\AdmissionController::update');
        $routes->delete('admissions', 'Api\V1\AdmissionController::delete');

        $routes->post('requirements', 'Api\V1\RequirementController::create');
        $routes->post('requirements/settings', 'Api\V1\RequirementController::settings');
        $routes->post('requirements/update', 'Api\V1\RequirementController::update');
        $routes->delete('requirements', 'Api\V1\RequirementController::delete');

        $routes->post('projects', 'Api\V1\ProjectController::create');
        $routes->post('projects/settings', 'Api\V1\ProjectController::settings');
        $routes->post('projects/update', 'Api\V1\ProjectController::update');
        $routes->delete('projects', 'Api\V1\ProjectController::delete');

        $routes->post('events', 'Api\V1\EventController::create');
        $routes->post('events/update', 'Api\V1\EventController::update');
        $routes->post('events/delete', 'Api\V1\EventController::delete');

        $routes->post('announcements', 'Api\V1\AnnouncementController::create');
        $routes->post('announcements/update', 'Api\V1\AnnouncementController::update');
        $routes->delete('announcements', 'Api\V1\AnnouncementController::delete');

        $routes->get('notifications', 'Api\V1\NotificationController::index');
        $routes->post('notifications/read', 'Api\V1\NotificationController::read');
    });
});
