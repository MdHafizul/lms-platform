<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LecturerProfile;
use App\Models\Course;
use App\Models\Enrollment;
use App\Support\Enums\UserRole;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // First seed roles and permissions
        $this->call(RolesAndPermissionsSeeder::class);

        // Create admin user
        $admin = User::create([
            'full_name' => 'Admin User',
            'email' => 'admin@lms.test',
            'password' => bcrypt('password123'),
            'phone_number' => '+1-000-000-0000',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create lecturer user
        $lecturer = User::create([
            'full_name' => 'Dr. John Teacher',
            'email' => 'lecturer@lms.test',
            'password' => bcrypt('password123'),
            'phone_number' => '+1-234-567-8901',
            'email_verified_at' => now(),
        ]);
        $lecturer->assignRole('lecturer');

        // Create lecturer profile
        $lecturerProfile = LecturerProfile::create([
            'user_id' => $lecturer->id,
            'specialization' => 'Web Development',
            'bio' => 'Experienced web development instructor',
            'office_hours' => 'Mon-Wed 2-4 PM',
            'contact_email' => 'john.teacher@university.edu',
            'phone' => '+1-234-567-8900',
        ]);

        // Create 3 student users
        $students = [];
        for ($i = 1; $i <= 3; $i++) {
            $student = User::create([
                'full_name' => "Student User $i",
                'email' => "student$i@lms.test",
                'password' => bcrypt('password123'),
                'phone_number' => "+1-234-567-890$i",
                'email_verified_at' => now(),
            ]);
            $student->assignRole('student');
            $students[] = $student;
        }

        // Create 2 courses
        $course1 = Course::create([
            'lecturer_id' => $lecturerProfile->id,
            'title' => 'Web Development Fundamentals',
            'code' => 'WEB101',
            'description' => 'Learn the basics of web development',
            'status' => 'published',
        ]);

        $course2 = Course::create([
            'lecturer_id' => $lecturerProfile->id,
            'title' => 'Advanced Laravel',
            'code' => 'LAR201',
            'description' => 'Master Laravel framework',
            'status' => 'draft',
        ]);

        // Enroll students in courses
        foreach ($students as $index => $student) {
            Enrollment::create([
                'user_id' => $student->id,
                'course_id' => $course1->id,
                'status' => 'ACTIVE',
                'enrolled_at' => now(),
            ]);

            if ($index < 2) {
                Enrollment::create([
                    'user_id' => $student->id,
                    'course_id' => $course2->id,
                    'status' => 'ACTIVE',
                    'enrolled_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Database seeding completed!');
        $this->command->info('');
        $this->command->info('TEST CREDENTIALS:');
        $this->command->info('─────────────────');
        $this->command->info('Admin: admin@lms.test / password123');
        $this->command->info('Lecturer: lecturer@lms.test / password123');
        $this->command->info('Student 1: student1@lms.test / password123');
        $this->command->info('Student 2: student2@lms.test / password123');
        $this->command->info('Student 3: student3@lms.test / password123');
    }
}
