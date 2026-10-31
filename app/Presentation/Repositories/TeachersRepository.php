<?php

namespace App\Presentation\Repositories;

use App\Controllers\TeachersController;
use DomainException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TeachersRepository
{

    function get_all_teachers(Request $req, Response $resp, $args): Response
    {
        $controller = new TeachersController();
        $rows = $controller->all();
        if (count($rows) == 0) {
            return $resp->withStatus(204);
        } else {
            $resp->getBody()->write($rows->toJson());
            return $resp
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function get_teacher(Request $req, Response $resp, $args): Response
    {
        $id = $args["id"];
        if (!ctype_digit($id)) {
            return $resp->withStatus(400);
        }
        $controller = new TeachersController();
        $row = $controller->find_teacher_by_id((int) $id);
        if (empty($row)) {
            return $resp->withStatus(404);
        } else {
            $resp->getBody()->write($row->toJson());
            return $resp
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    // GET /docentes/asignatura/{asignatura}
    function get_teachers_by_subject(Request $req, Response $resp, $args): Response
    {
        $subject = trim($args["asignatura"]);
        $controller = new TeachersController();
        $rows = $controller->find_by_subject($subject);
        if (count($rows) == 0) {
            return $resp->withStatus(204);
        } else {
            $resp->getBody()->write($rows->toJson());
            return $resp
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    // GET /docentes/buscar/{texto}
    function search_teacher(Request $req, Response $resp, $args): Response
    {
        $text = trim($args["texto"]);
        $controller = new TeachersController();
        $teachers = $controller->search_with_subjects($text);
        if (count($teachers) == 0) {
            return $resp->withStatus(404);
        } else {
            $resp->getBody()->write(json_encode($teachers, JSON_UNESCAPED_UNICODE));
            return $resp
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function create_teacher(Request $req, Response $resp, $args): Response
    {
        $body = $req->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$this->is_valid_teacher($data)) {
            return $resp->withStatus(400);
        }
        $controller = new TeachersController();
        try {
            $row = $controller->create($data);
        } catch (DomainException $e) {
            return $this->conflict($resp, $e->getMessage());
        }
        $resp->getBody()->write($row->toJson());
        return $resp
            ->withStatus(201)
            ->withHeader('Content-Type', 'application/json');
    }

    function update_teacher(Request $req, Response $resp, $args): Response
    {
        $id = $args["id"];
        if (!ctype_digit($id)) {
            return $resp->withStatus(400);
        }
        $body = $req->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$this->is_valid_teacher($data)) {
            return $resp->withStatus(400);
        }
        $controller = new TeachersController();
        try {
            $row = $controller->update((int) $id, $data);
        } catch (DomainException $e) {
            return $this->conflict($resp, $e->getMessage());
        }
        if (empty($row)) {
            return $resp->withStatus(404);
        } else {
            $resp->getBody()->write($row->toJson());
            return $resp
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function delete_teacher(Request $req, Response $resp, $args): Response
    {
        $id = $args["id"];
        if (!ctype_digit($id)) {
            return $resp->withStatus(400);
        }
        $controller = new TeachersController();
        $row = $controller->delete((int) $id);
        if (empty($row)) {
            return $resp->withStatus(404);
        } else {
            return $resp->withStatus(200);
        }
    }

    
    private function conflict(Response $resp, string $message): Response
    {
        $resp->getBody()->write(json_encode(['error' => $message], JSON_UNESCAPED_UNICODE));
        return $resp
            ->withStatus(409)
            ->withHeader('Content-Type', 'application/json');
    }

    // Valida el JSON recibido. Columnas: codigo int, nombre varchar(150), asignatura varchar(250)
    private function is_valid_teacher($data): bool
    {
        return is_array($data)
            && isset($data['codigo'], $data['nombre'], $data['asignatura'])
            && (is_int($data['codigo']) || (is_string($data['codigo']) && ctype_digit($data['codigo'])))
            && is_string($data['nombre']) && trim($data['nombre']) !== '' && strlen($data['nombre']) <= 150
            && is_string($data['asignatura']) && trim($data['asignatura']) !== '' && strlen($data['asignatura']) <= 250;
    }
}
