<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Repositories\CourseRepository;
use App\Repositories\CourseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Access\AuthorizationException;

class CourseService
{
    public function __construct(
        private CourseRepository $courseRepository
    )
    {}

    public function getAll(): Collection
    {
        return $this->courseRepository->getAll();
    }

    public function getAllByTeacher(int $teacherId): Collection
    {
        return $this->courseRepository->getAllByTeacher($teacherId);
    }

    public function create(array $data): Course
    {
        return $this->courseRepository->create($data);
    }

    
    public function getById(int $id): Course
    {
        return $this->courseRepository->findById($id);
    }


    public function update(int $id, int $teacherId, array $data): Course
    {
        $course = $this->courseRepository->findById($id);

        if ($course->teacher_id !== $teacherId) {
            throw new AuthorizationException('You are not authorized to update this course.');
        }

        return $this->courseRepository->update($id, $data);
    }

    public function delete(int $id, int $teacherId): bool
    {
        $course = $this->courseRepository->findById($id);

        if ($course->teacher_id !== $teacherId) {
            throw new AuthorizationException('You are not authorized to delete this course.');
        }

        return $this->courseRepository->delete($id);
    }
}
