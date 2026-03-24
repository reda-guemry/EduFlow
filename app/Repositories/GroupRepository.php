<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Group;

class GroupRepository implements GroupRepositoryInterface
{
    
    public function findAvailableGroupForCourse(int $courseId): ?Group
    {
        return Group::where('course_id', $courseId)
            ->where('student_count', '<', 25)
            ->first();
    }

    
    public function createGroup(int $courseId, string $name): Group
    {
        return Group::create([
            'course_id' => $courseId,
            'name' => $name,
            'student_count' => 0,
        ]);
    }

    public function addStudentToGroup(int $groupId, int $studentId): bool
    {
        $group = Group::findOrFail($groupId);

        $group->students()->attach($studentId);

        $group->increment('student_count');

        return true;
    }


    public function countGroupsForCourse(int $courseId): int
    {
        return Group::where('course_id', $courseId)->count();
    }
    
}
