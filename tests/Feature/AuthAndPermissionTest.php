<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_browse_projects_but_cannot_upload()
    {
        // Guest can access projects index
        $response = $this->get('/projects');
        $response->assertStatus(200);

        // Guest cannot access create project form
        $response = $this->get('/projects/create');
        $response->assertStatus(403);

        // Guest cannot ask a question
        $response = $this->post('/forum/question', ['content' => 'Test Question?']);
        $response->assertStatus(403);
    }

    public function test_student_can_ask_question_but_cannot_upload_project()
    {
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'student_id' => 'S12345',
            'department' => 'CS'
        ]);

        $this->actingAs($student);

        // Student can view create projects but gets 403 when trying to open upload page
        $response = $this->get('/projects/create');
        $response->assertStatus(403);

        // Student can post a question
        $response = $this->post('/forum/question', ['content' => 'How to do X?']);
        $response->assertRedirect('/forum');
        
        $this->assertDatabaseHas('questions', [
            'content' => 'How to do X?',
            'student_id' => $student->id
        ]);
    }

    public function test_graduate_can_upload_project_and_answer_question()
    {
        $graduate = User::create([
            'name' => 'Graduate User',
            'email' => 'graduate@test.com',
            'password' => bcrypt('password'),
            'role' => 'graduate',
            'job_title' => 'Developer',
            'company' => 'Tech Inc',
            'expertise' => 'Laravel',
            'graduation_year' => 2025
        ]);

        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'student_id' => 'S12345',
            'department' => 'CS'
        ]);

        $question = Question::create([
            'student_id' => $student->id,
            'content' => 'How to do X?'
        ]);

        $this->actingAs($graduate);

        // Graduate cannot open upload project page (only admin can now)
        $response = $this->get('/projects/create');
        $response->assertStatus(403);

        // Graduate can answer a question
        $response = $this->post("/forum/question/{$question->id}/answer", [
            'content' => 'Use library Y.'
        ]);
        $response->assertRedirect('/forum');

        $this->assertDatabaseHas('answers', [
            'content' => 'Use library Y.',
            'question_id' => $question->id,
            'user_id' => $graduate->id
        ]);
    }

    public function test_admin_can_access_dashboard_and_delete_users()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'student_id' => 'S12345',
            'department' => 'CS'
        ]);

        $this->actingAs($admin);

        // Admin can access dashboard
        $response = $this->get('/admin');
        $response->assertStatus(200);

        // Admin can view users list
        $response = $this->get('/admin/users');
        $response->assertStatus(200);

        // Admin can delete a user
        $response = $this->delete("/admin/users/{$student->id}");
        $response->assertRedirect('/admin/users');

        $this->assertDatabaseMissing('users', [
            'id' => $student->id
        ]);
    }

    public function test_student_registration_validation()
    {
        // 1. Test missing student_department
        $response = $this->post('/register', [
            'name' => 'New Student',
            'email' => 'newstudent@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'student',
            'student_id' => 'S12345',
        ]);
        
        $response->assertSessionHasErrors(['student_department']);
        
        // 2. Test successful student registration
        $response = $this->post('/register', [
            'name' => 'New Student',
            'email' => 'newstudent@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'student',
            'student_id' => 'S12345',
            'student_department' => 'هندسة برمجيات',
        ]);
        
        $response->assertRedirect('/projects');
        $this->assertDatabaseHas('users', [
            'email' => 'newstudent@test.com',
            'role' => 'student',
            'department' => 'هندسة برمجيات',
        ]);
    }

    public function test_graduate_cannot_register_directly()
    {
        $response = $this->post('/register', [
            'name' => 'New Graduate',
            'email' => 'newgraduate@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'graduate',
            'job_title' => 'Engineer',
            'company' => 'Google',
            'expertise' => 'Laravel',
            'graduation_year' => 2024,
        ]);
        
        $response->assertSessionHasErrors(['role']);
    }

    public function test_admin_can_create_user_accounts()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin_test@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $this->actingAs($admin);

        $response = $this->post('/admin/users', [
            'name' => 'Dr. Khalid',
            'email' => 'dr.khalid@test.com',
            'password' => 'password123',
            'role' => 'professor',
            'professor_id' => 'P999',
            'title' => 'Prof.',
            'professor_department' => 'Cybersecurity',
            'phone' => '0912345678'
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'email' => 'dr.khalid@test.com',
            'role' => 'professor',
            'professor_id' => 'P999',
            'phone' => '0912345678'
        ]);
    }

    public function test_admin_project_upload_with_auto_user_creation()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin_proj@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $professor = User::create([
            'name' => 'Ahmed Supervisor',
            'email' => 'ahmed_sup@test.com',
            'password' => bcrypt('password'),
            'role' => 'professor',
            'professor_id' => 'P222',
            'title' => 'Dr.',
            'department' => 'IT'
        ]);

        $this->actingAs($admin);

        // Fake file upload
        \Illuminate\Support\Facades\Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->create('project.pdf', 500);

        $response = $this->post('/projects', [
            'title' => 'AI System for Smart Agriculture',
            'description' => 'A comprehensive agricultural prediction model using IoT and AI.',
            'specialty' => 'Artificial Intelligence',
            'year' => 2026,
            'supervisor_id' => $professor->id,
            'file' => $file,
            'student_names' => [
                'Yaseen Mohammed',
                'Basma Ali'
            ]
        ]);

        $response->assertRedirect('/projects');

        // Check if accounts were automatically created for graduates
        $this->assertDatabaseHas('users', [
            'name' => 'Yaseen Mohammed',
            'email' => 'yaseen.mohammed@erth.com',
            'role' => 'graduate',
            'department' => 'Artificial Intelligence',
            'graduation_year' => 2026
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Basma Ali',
            'email' => 'basma.ali@erth.com',
            'role' => 'graduate',
            'department' => 'Artificial Intelligence',
            'graduation_year' => 2026
        ]);

        // Check if project exists
        $this->assertDatabaseHas('projects', [
            'title' => 'AI System for Smart Agriculture',
            'specialty' => 'Artificial Intelligence',
            'year' => 2026,
            'supervisor_id' => $professor->id
        ]);

        $project = Project::where('title', 'AI System for Smart Agriculture')->first();
        
        // Check if project-student relations are in pivot table
        $this->assertCount(2, $project->students);
    }
}
