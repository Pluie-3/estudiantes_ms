<?php

namespace App\Presentation\Repositories;

use App\Controllers\StudentsController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StudentsRepository
{

    function get_all_students(Request $req, Response $resp, $args): Response
    {
        $controller = new StudentsController();
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

    function get_student(Request $req, Response $resp, $args): Response
    {
        $id = $args["id"];
        if (!ctype_digit($id)) {
            return $resp->withStatus(400);
        }
        $controller = new StudentsController();
        $row = $controller->find_student_by_id((int) $id);
        if (empty($row)) {
            return $resp->withStatus(404);
        } else {
            $resp->getBody()->write($row->toJson());
            return $resp
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function create_student(Request $req, Response $resp, $args): Response
    {
        $body = $req->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$this->is_valid_student($data)) {
            return $resp->withStatus(400);
        }
        $controller = new StudentsController();
        $row = $controller->create($data);
        if (empty($row)) {
            return $resp->withStatus(400);
        } else {
            $resp->getBody()->write($row->toJson());
            return $resp
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function update_student(Request $req, Response $resp, $args): Response
    {
        $id = $args["id"];
        if (!ctype_digit($id)) {
            return $resp->withStatus(400);
        }
        $body = $req->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$this->is_valid_student($data)) {
            return $resp->withStatus(400);
        }
        $controller = new StudentsController();
        $row = $controller->update((int) $id, $data);
        if (empty($row)) {
            return $resp->withStatus(404);
        } else {
            $resp->getBody()->write($row->toJson());
            return $resp
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function delete_student(Request $req, Response $resp, $args): Response
    {
        $id = $args["id"];
        if (!ctype_digit($id)) {
            return $resp->withStatus(400);
        }
        $controller = new StudentsController();
        $row = $controller->delete((int) $id);
        if (empty($row)) {
            return $resp->withStatus(404);
        } else {
            return $resp->withStatus(200);
        }
    }


    
    private function is_valid_student($data): bool
    {
        return is_array($data)
            && isset($data['codigo'], $data['nombre'], $data['email'])
            && is_string($data['codigo']) && trim($data['codigo']) !== '' && strlen($data['codigo']) <= 4
            && is_string($data['nombre']) && trim($data['nombre']) !== '' && strlen($data['nombre']) <= 150
            && is_string($data['email']) && strlen($data['email']) <= 150
            && filter_var($data['email'], FILTER_VALIDATE_EMAIL) !== false;
    }
}
