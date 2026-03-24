<?php


namespace App\Repositories;

use App\Models\Group;

interface GroupRepositoryInterface
{
    
    public function findAvailableGroupForCourse(int $courseId): ?Group;

   
    public function createGroup(int $courseId, string $name): Group;

   
    public function addStudentToGroup(int $groupId, int $studentId): bool;

    
    public function countGroupsForCourse(int $courseId): int;
}
