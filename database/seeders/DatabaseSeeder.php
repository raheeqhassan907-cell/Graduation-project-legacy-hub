<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin
        $admin = User::create([
            'name' => 'Raheeq Hassan',
            'email' => 'raheegohassan@gmail.com',
            'password' => bcrypt('raheego'),
            'role' => 'admin',
        ]);

        // 2. Professors
        $prof1 = User::create([
            'name' => 'أحمد علي',
            'email' => 'dr.ahmed@erth.com',
            'password' => bcrypt('password'),
            'role' => 'professor',
            'professor_id' => 'P101',
            'title' => 'د.',
            'department' => 'تقنية معلومات',
        ]);

        $prof2 = User::create([
            'name' => 'سارة كمال',
            'email' => 'dr.sarah@erth.com',
            'password' => bcrypt('password'),
            'role' => 'professor',
            'professor_id' => 'P102',
            'title' => 'د.',
            'department' => 'علوم حاسوب',
        ]);

        // 3. Graduates
        $grad1 = User::create([
            'name' => 'خالد محمود',
            'email' => 'khaled@erth.com',
            'password' => bcrypt('password'),
            'role' => 'graduate',
            'job_title' => 'مطور تطبيقات ويب',
            'company' => 'حلول التقنية',
            'expertise' => 'Laravel, MySQL, JavaScript',
            'graduation_year' => 2024,
            'department' => 'تقنية معلومات',
        ]);

        $grad2 = User::create([
            'name' => 'منى يوسف',
            'email' => 'mona@erth.com',
            'password' => bcrypt('password'),
            'role' => 'graduate',
            'job_title' => 'مهندسة برمجيات',
            'company' => 'ريادة للحلول',
            'expertise' => 'Python, AI, Git',
            'graduation_year' => 2025,
            'department' => 'علوم حاسوب',
        ]);

        // 4. Students
        $student1 = User::create([
            'name' => 'عمر عادل',
            'email' => 'omar@erth.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'student_id' => 'S202301',
            'department' => 'تقنية معلومات',
        ]);

        $student2 = User::create([
            'name' => 'ليلى محمد',
            'email' => 'laila@erth.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'student_id' => 'S202302',
            'department' => 'علوم حاسوب',
        ]);

        // 5. Projects
        $proj1 = \App\Models\Project::create([
            'title' => 'نظام إدارة الموارد للمستشفيات الذكية',
            'description' => "نظام رقمي سحابي متكامل يهدف لأتمتة العمليات اليومية في المستشفيات وإدارة الموارد بكفاءة عالية. يحتوي النظام على وحدات لإدارة ملفات المرضى الإلكترونية وجدولة المواعيد للأطباء ومتابعة مخزون الأدوية والأدوات الطبية بصورة آلية مع إمكانية استخراج تقارير إحصائية متكاملة لمديري الرعاية الصحية.\n\nتميز المشروع بواجهة مستخدم متجاوبة وبنية قواعد بيانات مرنة تتحمل أعداداً كبيرة من السجلات في اللحظة ذاتها.",
            'specialty' => 'تقنية معلومات',
            'technologies' => 'Laravel, Vue.js, MySQL, Bootstrap',
            'year' => 2024,
            'graduate_id' => $grad1->id,
            'supervisor_id' => $prof1->id,
        ]);
        $proj1->students()->attach($grad1->id);

        $proj2 = \App\Models\Project::create([
            'title' => 'تطبيق التعرف على الصور الطبية باستخدام الذكاء الاصطناعي',
            'description' => "بحث تطبيقي ونظام حاسوبي يقوم بتحليل الصور الإشعاعية لتشخيص الأورام المبكرة بدقة تفوق 95% باستخدام خوارزميات التعلم العميق. يعتمد النظام على شبكة عصبية تلافيفية (CNN) تم تدريبها على آلاف الصور المفتوحة والموثقة طبياً.\n\nيسهل التطبيق عمل أطباء الأشعة عبر توفير واجهة ويب تتيح لهم رفع الصور الإشعاعية والحصول على تحليل فوري ومقترحات أولية للتشخيص الطبي لتقليص الأخطاء البشرية ووقت الانتظار.",
            'specialty' => 'علوم حاسوب',
            'technologies' => 'Python, TensorFlow, Flask, TailwindCSS',
            'year' => 2025,
            'graduate_id' => $grad2->id,
            'supervisor_id' => $prof2->id,
        ]);
        $proj2->students()->attach($grad2->id);

        // 6. Questions
        $q1 = \App\Models\Question::create([
            'student_id' => $student1->id,
            'content' => 'ما هي المتطلبات الأساسية لربط نظام دفع إلكتروني بتطبيق لارافيل؟ وكيف يمكن البدء بذلك؟',
        ]);

        $q2 = \App\Models\Question::create([
            'student_id' => $student2->id,
            'content' => 'هل الأفضل لمشروع التخرج استخدام قاعدة بيانات NoSQL (مثل MongoDB) أم قاعدة بيانات SQL (مثل MySQL) لموقع تجارة إلكترونية؟',
        ]);

        // 7. Answers
        \App\Models\Answer::create([
            'question_id' => $q1->id,
            'user_id' => $grad1->id,
            'content' => "أهلاً عمر، للبدء تحتاج أولاً لاختيار بوابة الدفع (مثل Stripe أو Paytabs). أغلب البوابات توفر حزماً برمجية (SDK) خاصة بلغة PHP ولارافيل.\n\nيمكنك تثبيت حزمة Stripe الرسمية عبر Composer وتخزين مفاتيح الربط (API Keys) في ملف .env، ثم إنشاء Controller يتعامل مع طلبات الدفع وإرجاع حالة العملية للمستخدم. هناك العديد من الشروحات التفصيلية في التوثيق الرسمي لكل بوابة.",
        ]);

        \App\Models\Answer::create([
            'question_id' => $q2->id,
            'user_id' => $prof2->id,
            'content' => "أهلاً ليلى. لموقع تجارة إلكترونية، أنصحك بشدة باستخدام قاعدة بيانات علاقة (SQL) مثل MySQL أو PostgreSQL. التجارة الإلكترونية تتطلب معاملات مالية صارمة (ACID Transactions) وعلاقات وثيقة بين الجداول (مثل علاقة الطلب بالمستخدم والمنتجات).\n\nبينما NoSQL ممتازة للبيانات غير المهيكلة أو التحليلات الضخمة، لكنها قد تسبب مشاكل تماسك البيانات في الأنظمة التجارية الحساسة.",
        ]);
    }
}
