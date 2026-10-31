<?php

use App\Presentation\Repositories\TeachersRepository;

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->group('/docentes', function (RouteCollectorProxy $group) {
        $group->get('', [TeachersRepository::class, 'get_all_teachers']);
        $group->get('/asignatura/{asignatura}', [TeachersRepository::class, 'get_teachers_by_subject']);
        $group->get('/buscar/{texto}', [TeachersRepository::class, 'search_teacher']);
        $group->get('/{id}', [TeachersRepository::class, 'get_teacher']);
        $group->post('', [TeachersRepository::class, 'create_teacher']);
        $group->put('/{id}', [TeachersRepository::class, 'update_teacher']);
        $group->delete('/{id}', [TeachersRepository::class, 'delete_teacher']);
    });
};
