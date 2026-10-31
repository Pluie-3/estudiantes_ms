<?php

namespace App\Controllers;

use App\Models\Teacher;
use DomainException;
use Illuminate\Database\Eloquent\Collection;

class TeachersController
{
    function all(): Collection
    {
        return Teacher::all();
    }

    function find_teacher_by_id(int $id): Teacher|null
    {
        return Teacher::find($id);
    }


    function find_by_subject(string $subject): Collection
    {
        return Teacher::where('asignatura', 'like', '%' . $subject . '%')
            ->orderBy('nombre')
            ->get();
    }


    function search_with_subjects(string $text): array
    {
        $query = Teacher::where('nombre', 'like', '%' . $text . '%');
        if (ctype_digit($text)) {
            $query->orWhere('codigo', (int) $text);
        }
        $rows = $query->orderBy('nombre')->get();

        $result = [];
        foreach ($rows as $row) {
            $key = $row->codigo;
            if (!isset($result[$key])) {
                $result[$key] = [
                    'codigo' => $row->codigo,
                    'nombre' => $row->nombre,
                    'asignaturas' => []
                ];
            }
            $result[$key]['asignaturas'][] = $row->asignatura;
        }
        return array_values($result);
    }

    function create(array $data): Teacher
    {
        $codigo = (int) $data['codigo'];
        $asignatura = trim($data['asignatura']);
        if ($this->assignment_exists($codigo, $asignatura)) {
            throw new DomainException('El docente ya tiene asignada esa asignatura');
        }
        $teacher = new Teacher();
        $teacher->codigo = $codigo;
        $teacher->nombre = trim($data['nombre']);
        $teacher->asignatura = $asignatura;
        $teacher->save();
        return $teacher;
    }

    function update(int $id, array $data): Teacher|null
    {
        $teacher = Teacher::find($id);
        if (empty($teacher)) {
            return null;
        }
        $codigo = (int) $data['codigo'];
        $asignatura = trim($data['asignatura']);
        if ($this->assignment_exists($codigo, $asignatura, $id)) {
            throw new DomainException('El docente ya tiene asignada esa asignatura');
        }
        $teacher->codigo = $codigo;
        $teacher->nombre = trim($data['nombre']);
        $teacher->asignatura = $asignatura;
        $teacher->save();
        return $teacher;
    }

    function delete(int $id): Teacher|null
    {
        $teacher = Teacher::find($id);
        if (empty($teacher)) {
            return null;
        }
        $teacher->delete();
        return $teacher;
    }
    
    private function assignment_exists(int $codigo, string $asignatura, int $except_id = 0): bool
    {
        return Teacher::where('codigo', $codigo)
            ->where('asignatura', $asignatura)
            ->where('id', '!=', $except_id)
            ->exists();
    }
}
