<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $lecturerRole = Role::create(['name' => 'lecturer', 'guard_name' => 'web']);
        $studentRole = Role::create(['name' => 'student', 'guard_name' => 'web']);

        // Create permissions
        $permissions = [
            // Course permissions
            'create_course',
            'edit_course',
            'delete_course',
            'view_courses',
            'publish_course',
            'archive_course',

            // Assessment permissions
            'create_assessment',
            'edit_assessment',
            'delete_assessment',
            'view_assessments',
            'grade_assessment',

            // Assignment permissions
            'create_assignment',
            'edit_assignment',
            'delete_assignment',
            'view_assignments',
            'grade_assignment',

            // Enrollment permissions
            'create_enrollment',
            'edit_enrollment',
            'delete_enrollment',
            'view_enrollments',

            // Admin permissions
            'view_audit_logs',
            'assign_role',
            'override_grade',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());

        $lecturerRole->givePermissionTo([
            'create_course',
            'edit_course',
            'delete_course',
            'view_courses',
            'publish_course',
            'archive_course',
            'create_assessment',
            'edit_assessment',
            'delete_assessment',
            'view_assessments',
            'grade_assessment',
            'create_assignment',
            'edit_assignment',
            'delete_assignment',
            'view_assignments',
            'grade_assignment',
            'view_enrollments',
        ]);

        $studentRole->givePermissionTo([
            'view_courses',
            'view_assessments',
            'view_assignments',
            'view_enrollments',
        ]);
    }
}
