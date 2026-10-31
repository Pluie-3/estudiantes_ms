<?php

namespace App\Controllers;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

class StudentsController
{
    function all(): Collection
    {
        return Student::all();
    }

    function find_student_by_id(int $id): Student|null
    {
        return Student::find($id);
    }

    function create(array $data): Student
    {
        $student = new Student();
        $student->nombre = $data['nombre'];
        $student->codigo = $data['codigo'];
        $student->email = $data['email'];
        $student->save();
        return $student;
    }

    function update(int $id, array $data): Student|null
    {
        $student = Student::find($id);
        if (empty($student)) {
            return null;
        }
        $student->nombre = $data['nombre'];
        $student->codigo = $data['codigo'];
        $student->email = $data['email'];
        $student->save();
        return $student;
    }

    function delete(int $id): Student|null
    {
        $student = Student::find($id);
        if (empty($student)) {
            return null;
        }
        $student->delete();
        return $student;
    }
}
