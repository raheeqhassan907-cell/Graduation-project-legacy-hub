<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Force the environment key to be empty during test suite execution
        // to prevent tests from hitting the real Gemini API when a key is set in .env.
        $_ENV['GEMINI_API_KEY'] = null;
        $_SERVER['GEMINI_API_KEY'] = null;
        putenv('GEMINI_API_KEY=');
    }

    public function test_chatbot_greeting_query()
    {
        $response = $this->postJson('/chatbot/query', [
            'message' => 'مرحبا بك'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['reply']);
        $this->assertStringContainsString('مركز إرث', $response->json('reply'));
    }

    public function test_chatbot_developer_query()
    {
        $response = $this->postJson('/chatbot/query', [
            'message' => 'من هو مطور المنصة؟'
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('Raheeq Hassan', $response->json('reply'));
    }

    public function test_chatbot_analysis_query_with_no_projects()
    {
        $response = $this->postJson('/chatbot/query', [
            'message' => 'حلل لي مشاريع المنصة'
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('لا توجد مشاريع مضافة', $response->json('reply'));
    }

    public function test_chatbot_analysis_query_with_projects()
    {
        // Seed supervisor and project
        $professor = User::create([
            'name' => 'Dr. Ahmed',
            'email' => 'ahmed@test.com',
            'password' => bcrypt('password'),
            'role' => 'professor',
            'title' => 'Dr.',
            'department' => 'CS'
        ]);

        $project = Project::create([
            'title' => 'AI Classification Project',
            'description' => 'Using neural networks.',
            'specialty' => 'Artificial Intelligence',
            'technologies' => 'Python, PyTorch',
            'year' => 2026,
            'supervisor_id' => $professor->id
        ]);

        $response = $this->postJson('/chatbot/query', [
            'message' => 'احصائيات المشاريع'
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('مشاريع التخرج الموثقة', $response->json('reply'));
        $this->assertStringContainsString('Artificial Intelligence', $response->json('reply'));
    }

    public function test_chatbot_predictions_query()
    {
        $response = $this->postJson('/chatbot/query', [
            'message' => 'ما هي التوقعات والتريند القادم؟'
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('التوقعات والتنبؤات التقنية', $response->json('reply'));
        $this->assertStringContainsString('الذكاء الاصطناعي التوليدي', $response->json('reply'));
    }

    public function test_chatbot_upload_guidelines_query()
    {
        $response = $this->postJson('/chatbot/query', [
            'message' => 'طريقة رفع مشروع التخرج'
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('صلاحيات رفع مشاريع التخرج', $response->json('reply'));
        $this->assertStringContainsString('مدير النظام', $response->json('reply'));
    }

    public function test_chatbot_project_search_query()
    {
        $professor = User::create([
            'name' => 'Dr. Sara',
            'email' => 'sara@test.com',
            'password' => bcrypt('password'),
            'role' => 'professor',
            'title' => 'Dr.',
            'department' => 'IT'
        ]);

        $project = Project::create([
            'title' => 'Web App for Smart Health',
            'description' => 'Medical monitoring application.',
            'specialty' => 'Information Technology',
            'technologies' => 'Laravel, Vue',
            'year' => 2026,
            'supervisor_id' => $professor->id
        ]);

        $response = $this->postJson('/chatbot/query', [
            'message' => 'ابحث عن مشروع Smart Health'
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('Smart Health', $response->json('reply'));
        $this->assertStringContainsString('Information Technology', $response->json('reply'));
    }

    public function test_chatbot_with_gemini_api_active()
    {
        // 1. Force environment variable for this test
        $_ENV['GEMINI_API_KEY'] = 'test_mock_gemini_key';
        $_SERVER['GEMINI_API_KEY'] = 'test_mock_gemini_key';
        putenv('GEMINI_API_KEY=test_mock_gemini_key');

        // 2. Mock Http call to Gemini
        \Illuminate\Support\Facades\Http::fake([
            'generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'هذه إجابة ذكاء اصطناعي تجريبية من Gemini.']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson('/chatbot/query', [
            'message' => 'ما هي عاصمة السودان؟'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['reply']);
        $this->assertStringContainsString('Gemini', $response->json('reply'));

        // 3. Clear environment variable
        $_ENV['GEMINI_API_KEY'] = null;
        $_SERVER['GEMINI_API_KEY'] = null;
        putenv('GEMINI_API_KEY=');
    }
}
