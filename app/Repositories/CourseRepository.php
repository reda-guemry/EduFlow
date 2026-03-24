<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

class CourseRepository 
{
    public function getAll(): Collection
    {
        return Course::all();
    }
    public function getAllByTeacher(int $teacherId): Collection
    {
        return Course::where('teacher_id', $teacherId)->get();
    }


    public function create(array $data): Course
    {
        return Course::create($data);
    }


    public function findById(int $id): ?Course
    {
        return Course::findOrFail($id);
    }

    public function update(int $id, array $data): ?Course
    {
        $course = Course::findOrFail($id);
        $course->update($data);
        return $course;
    }

    
    public function delete(int $id): bool
    {
        $course = Course::findOrFail($id);
        return (bool) $course->delete();
    }
}
