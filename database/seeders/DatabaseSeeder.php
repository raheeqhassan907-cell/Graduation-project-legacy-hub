<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Question;
use App\Models\Answer;
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

        // 2. Original Professors
        $prof1 = User::create([
            'name' => 'أحمد علي',
            'email' => 'dr.ahmed@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'professor',
            'professor_id' => 'P101',
            'title' => 'د.',
            'department' => 'تقنية معلومات',
        ]);

        $prof2 = User::create([
            'name' => 'سارة كمال',
            'email' => 'dr.sarah@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'professor',
            'professor_id' => 'P102',
            'title' => 'د.',
            'department' => 'علوم حاسوب',
        ]);

        // 3. New Supervisors from PDFs
        $profNahla = User::create([
            'name' => 'نهلة عثمان الشفيع',
            'email' => 'dr.nahla@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'professor',
            'professor_id' => 'P103',
            'title' => 'د.',
            'department' => 'إدارة الأعمال',
        ]);

        $profRasha = User::create([
            'name' => 'رشا إبراهيم',
            'email' => 'dr.rasha@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'professor',
            'professor_id' => 'P104',
            'title' => 'د.',
            'department' => 'تقنية معلومات',
        ]);

        $profManal = User::create([
            'name' => 'Manal Mohammed Ali',
            'email' => 'dr.manal@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'professor',
            'professor_id' => 'P105',
            'title' => 'Dr.',
            'department' => 'الأحياء الدقيقة',
        ]);

        $profNour = User::create([
            'name' => 'نور الدين عبد الرازق',
            'email' => 'nour@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'professor',
            'professor_id' => 'P106',
            'title' => 'أ.',
            'department' => 'تقنية معلومات',
        ]);

        $profMuhasin = User::create([
            'name' => 'Dr. Muhasin Ahmed Mohmmed Almakii',
            'email' => 'dr.muhasin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'professor',
            'professor_id' => 'P107',
            'title' => 'Dr.',
            'department' => 'الطب والجراحة',
        ]);

        $profFatima = User::create([
            'name' => 'Fatima Mohammed Abu',
            'email' => 'dr.fatima.abu@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'professor',
            'professor_id' => 'P108',
            'title' => 'Dr.',
            'department' => 'علوم التمريض',
        ]);

        // 4. Original Graduates
        $grad1 = User::create([
            'name' => 'خالد محمود',
            'email' => 'khaled@gmail.com',
            'password' => bcrypt('25190001'),
            'role' => 'graduate',
            'student_id' => '25190001',
            'job_title' => 'مطور تطبيقات ويب',
            'company' => 'حلول التقنية',
            'expertise' => 'Laravel, MySQL, JavaScript',
            'graduation_year' => 2024,
            'department' => 'تقنية معلومات',
        ]);

        $grad2 = User::create([
            'name' => 'منى يوسف',
            'email' => 'mona@gmail.com',
            'password' => bcrypt('25190002'),
            'role' => 'graduate',
            'student_id' => '25190002',
            'job_title' => 'مهندسة برمجيات',
            'company' => 'ريادة للحلول',
            'expertise' => 'Python, AI, Git',
            'graduation_year' => 2025,
            'department' => 'علوم حاسوب',
        ]);

        // 5. Original Students
        $student1 = User::create([
            'name' => 'عمر عادل',
            'email' => 'omar@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'student_id' => 'S202301',
            'department' => 'تقنية معلومات',
        ]);

        $student2 = User::create([
            'name' => 'ليلى محمد',
            'email' => 'laila@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'student_id' => 'S202302',
            'department' => 'علوم حاسوب',
        ]);

        // 6. Original seeded projects
        $proj1 = Project::create([
            'title' => 'نظام إدارة الموارد للمستشفيات الذكية',
            'description' => "نظام رقمي سحابي متكامل يهدف لأتمتة العمليات اليومية في المستشفيات وإدارة الموارد بكفاءة عالية. يحتوي النظام على وحدات لإدارة ملفات المرضى الإلكترونية وجدولة المواعيد للأطباء ومتابعة مخزون الأدوية والأدوات الطبية بصورة آلية مع إمكانية استخراج تقارير إحصائية متكاملة لمديري الرعاية الصحية.\n\nتميز المشروع بواجهة مستخدم متجاوبة وبنية قواعد بيانات مرنة تتحمل أعداداً كبيرة من السجلات في اللحظة ذاتها.",
            'specialty' => 'تقنية معلومات',
            'technologies' => 'Laravel, Vue.js, MySQL, Bootstrap',
            'grade' => 'A+',
            'year' => 2024,
            'graduate_id' => $grad1->id,
            'supervisor_id' => $prof1->id,
            'file_url' => 'uploads/projects/legacy_hub_project.pdf',
        ]);
        $proj1->students()->attach($grad1->id);

        $proj2 = Project::create([
            'title' => 'تطبيق التعرف على الصور الطبية باستخدام الذكاء الاصطناعي',
            'description' => "بحث تطبيقي ونظام حاسوبي يقوم بتحليل الصور الإشعاعية لتشخيص الأورام المبكرة بدقة تفوق 95% باستخدام خوارزميات التعلم العميق. يعتمد النظام على شبكة عصبية تلافيفية (CNN) تم تدريبها على آلاف الصور المفتوحة والموثقة طبياً.\n\nيسهل التطبيق عمل أطباء الأشعة عبر توفير واجهة ويب تتيح لهم رفع الصور الإشعاعية والحصول على تحليل فوري ومقترحات أولية للتشخيص الطبي لتقليص الأخطاء البشرية ووقت الانتظار.",
            'specialty' => 'علوم حاسوب',
            'technologies' => 'Python, TensorFlow, Flask, TailwindCSS',
            'grade' => 'A',
            'year' => 2025,
            'graduate_id' => $grad2->id,
            'supervisor_id' => $prof2->id,
            'file_url' => 'uploads/projects/legacy_hub_project.pdf',
        ]);
        $proj2->students()->attach($grad2->id);


        // ==========================================
        // NEW PROJECTS FROM USER UPLOADED DOCUMENTS
        // ==========================================

        // --- Project 1: أثر إدارة المعرفة على أداء العاملين ---
        $p1_students = [
            'محمد الطيب التجاني' => ['email' => 'mohammed.altayeb@gmail.com', 'id' => '25210001'],
            'ابوبكر صابر جبارة' => ['email' => 'abubakr.saber@gmail.com', 'id' => '25210002'],
            'سعيد محمد هاشم محمد' => ['email' => 'saeed.mohammed@gmail.com', 'id' => '25210003'],
            'عبد العزيز محمد سيد' => ['email' => 'abdelaziz.mohammed@gmail.com', 'id' => '25210004'],
        ];
        $p1_student_ids = [];
        foreach ($p1_students as $name => $data) {
            $user = User::create([
                'name' => $name,
                'email' => $data['email'],
                'password' => bcrypt($data['id']),
                'role' => 'graduate',
                'student_id' => $data['id'],
                'department' => 'إدارة الأعمال',
                'graduation_year' => 2024,
            ]);
            $p1_student_ids[] = $user->id;
        }

        $pdfProj1 = Project::create([
            'title' => 'أثر إدارة المعرفة على أداء العاملين بالهيئة محل الدراسة',
            'description' => "هدفت هذه الدراسة إلى التعرف على دور إدارة المعرفة في تحسين أداء العاملين ولتحقيق هدف الدراسة تم تصميم استمارة استقصاء وزعت على عينة الدراسة البالغة 320 مفردة من العاملين بالهيئة محل الدراسة.\n\nوحللت البيانات بواسطة البرنامج الإحصائي SPSS ومن أهم نتائج الدراسة وجود علاقة ذات دلالة إحصائية بين إدارة المعرفة وتحسين أداء العاملين، وكذا وجود قصور في توافر الكفاءات والكوادر البشرية المدربة والتدفق المعرفي بالهيئة في ضوء تدعيم إدارة المعرفة.",
            'specialty' => 'إدارة الأعمال',
            'technologies' => 'SPSS, MS Excel',
            'grade' => 'A',
            'year' => 2024,
            'graduate_id' => $p1_student_ids[0],
            'supervisor_id' => $profNahla->id,
            'file_url' => 'uploads/projects/knowledge_management_performance.pdf',
        ]);
        $pdfProj1->students()->sync($p1_student_ids);


        // --- Project 2: وكالة سفر وسياحة منظمة (Travelease) ---
        $p2_students = [
            'إنتصار هشام محجوب محمد الحسن' => ['email' => 'intisar.hisham@gmail.com', 'id' => '25210011'],
            'محمد حيدر المصباح العبيد' => ['email' => 'mohammed.haidar@gmail.com', 'id' => '25210012'],
            'عبد الرحمن عبد الحليم شيخ محمد شاطر' => ['email' => 'abdelrahman.abdelhalim@gmail.com', 'id' => '25210013'],
        ];
        $p2_student_ids = [];
        foreach ($p2_students as $name => $data) {
            $user = User::create([
                'name' => $name,
                'email' => $data['email'],
                'password' => bcrypt($data['id']),
                'role' => 'graduate',
                'student_id' => $data['id'],
                'department' => 'تقنية معلومات',
                'graduation_year' => 2025,
            ]);
            $p2_student_ids[] = $user->id;
        }

        $pdfProj2 = Project::create([
            'title' => 'وكالة سفر وسياحة منظمة (Travelease)',
            'description' => "مشروع تخرج يهدف إلى تصميم وتطوير نظام متكامل لحجز وإدارة وكالات السفر والسياحة إلكترونياً (Travelease).\n\nيعالج النظام المشاكل التقليدية الورقية مثل صعوبة إدارة وتتبع الحجوزات يدوياً، بطء الإجراءات، وضياع البيانات. يوفر النظام لوحات تحكم للمسؤولين وللعملاء لإجراء حجوزات الفنادق والرحلات الجوية ومتابعتها بكل سهولة وبأمان عالي.",
            'specialty' => 'تقنية معلومات',
            'technologies' => 'PHP, HTML, CSS, JavaScript, MySQL, Apache',
            'grade' => 'A+',
            'year' => 2025,
            'graduate_id' => $p2_student_ids[0],
            'supervisor_id' => $profRasha->id,
            'file_url' => 'uploads/projects/travelease_agency.pdf',
        ]);
        $pdfProj2->students()->sync($p2_student_ids);


        // --- Project 3: Detection And Isolation Of Antimicrobial Producer From Soil ---
        $p3_students = [
            'Alwasela Atif Musa' => ['email' => 'alwasela.atif@gmail.com', 'id' => '25210021'],
            'Mustafa Ahmed Farooq' => ['email' => 'mustafa.ahmed@gmail.com', 'id' => '25210022'],
            'Samah Esam Ahmaidi' => ['email' => 'samah.esam@gmail.com', 'id' => '25210023'],
            'Tamadhur Adil Abdallah' => ['email' => 'tamadhur.adil@gmail.com', 'id' => '25210024'],
            'Yasir Omer Abdallah' => ['email' => 'yasir.omer@gmail.com', 'id' => '25210025'],
        ];
        $p3_student_ids = [];
        foreach ($p3_students as $name => $data) {
            $user = User::create([
                'name' => $name,
                'email' => $data['email'],
                'password' => bcrypt($data['id']),
                'role' => 'graduate',
                'student_id' => $data['id'],
                'department' => 'علوم المختبرات الطبية',
                'graduation_year' => 2025,
            ]);
            $p3_student_ids[] = $user->id;
        }

        $pdfProj3 = Project::create([
            'title' => 'Detection And Isolation Of Antimicrobial Producer From Soil Collected From Different Areas In Sudan',
            'description' => "This scientific thesis focuses on the screening, detection, and isolation of antibiotic-producing microorganisms from soil samples collected across different geographic regions in Sudan.\n\nThe research investigates the effectiveness of isolated microorganisms (including Bacillus species and various fungi) against multidrug-resistant clinical bacterial pathogens (like Staphylococcus aureus, E. coli, and Klebsiella pneumoniae) using the Dutch Streak Method and susceptibility assays.",
            'specialty' => 'علوم المختبرات الطبية',
            'technologies' => 'Microbiology Lab Tools, Susceptibility Testing, Dutch Streak Method',
            'grade' => 'A+',
            'year' => 2025,
            'graduate_id' => $p3_student_ids[0],
            'supervisor_id' => $profManal->id,
            'file_url' => 'uploads/projects/antimicrobial_soil_sudan.pdf',
        ]);
        $pdfProj3->students()->sync($p3_student_ids);


        // --- Project 4: منصة التجارة الإلكترونية الاجتماعية ---
        $p4_students = [
            'محمد المبارك عبد الرحمن الطيب عبد الله' => ['email' => 'mohammed.almobarak@gmail.com', 'id' => '25200050'],
            'محمد بابكر وقيع الله أحمد' => ['email' => 'mohammed.babiker@gmail.com', 'id' => '25200051'],
            'أسامة عبد الدائم عبد الله حمد' => ['email' => 'osama.abdeldaim@gmail.com', 'id' => '25200086'],
        ];
        $p4_student_ids = [];
        foreach ($p4_students as $name => $data) {
            $user = User::create([
                'name' => $name,
                'email' => $data['email'],
                'password' => bcrypt($data['id']),
                'role' => 'graduate',
                'student_id' => $data['id'],
                'department' => 'تقنية معلومات',
                'graduation_year' => 2025,
            ]);
            $p4_student_ids[] = $user->id;
        }

        $pdfProj4 = Project::create([
            'title' => 'منصة التجارة الإلكترونية الاجتماعية',
            'description' => "منصة إلكترونية متكاملة تدمج بين خصائص التجارة الإلكترونية التقليدية وميزات التفاعل في شبكات التواصل الاجتماعي (Social Commerce).\n\nتسهل المنصة عملية استعراض وتصنيف كافة أنواع المنتجات الأكاديمية والاستهلاكية وخاصة ملحقات الطاقة الشمسية، وتتيح للبائعين إدارة منتجاتهم والتواصل مع المشترين، مع إتاحة ميزات التعليقات، الإعجابات والمشاركة لبناء مجتمع تسوق رقمي موثوق.",
            'specialty' => 'تقنية معلومات',
            'technologies' => 'React, Node.js, Express.js, PostgreSQL, Sequelize ORM',
            'grade' => 'A',
            'year' => 2025,
            'graduate_id' => $p4_student_ids[0],
            'supervisor_id' => $profNour->id,
            'file_url' => 'uploads/projects/social_e_commerce.pdf',
        ]);
        $pdfProj4->students()->sync($p4_student_ids);


        // --- Project 5: تطبيق بنك الدم السوداني ---
        $p5_students = [
            'آلاء طارق عثمان أحمد' => ['email' => 'alaa.tariq@gmail.com', 'id' => '25200091'],
            'حسن البشير حسن محمد' => ['email' => 'hassan.albashir@gmail.com', 'id' => '25200092'],
            'سند عبد العظيم ادم محمد' => ['email' => 'sanad.abdelazim@gmail.com', 'id' => '25200093'],
        ];
        $p5_student_ids = [];
        foreach ($p5_students as $name => $data) {
            $user = User::create([
                'name' => $name,
                'email' => $data['email'],
                'password' => bcrypt($data['id']),
                'role' => 'graduate',
                'student_id' => $data['id'],
                'department' => 'تقنية معلومات',
                'graduation_year' => 2022,
            ]);
            $p5_student_ids[] = $user->id;
        }

        $pdfProj5 = Project::create([
            'title' => 'تطبيق بنك الدم السوداني للهواتف الذكية',
            'description' => "تطبيق للهواتف المحمولة يهدف لحل مشاكل التبرع بالدم والحصول على الفصائل في السودان.\n\nيسهل التطبيق عملية البحث الجغرافي عن المتبرعين الأقرب للمستلم وربطهم فورياً لتقليل الوقت والجهد المهدر في حالات الطوارئ الحساسة. يدير التطبيق ملفات المتبرعين وفصائلهم وموقعهم الجغرافي المسجل مع ميزات إشعارات طلبات التبرع.",
            'specialty' => 'تقنية معلومات',
            'technologies' => 'Java, Android Studio, Firebase Realtime Database',
            'grade' => 'B+',
            'year' => 2022,
            'graduate_id' => $p5_student_ids[0],
            'supervisor_id' => $profNour->id,
            'file_url' => 'uploads/projects/sudan_blood_bank.pdf',
        ]);
        $pdfProj5->students()->sync($p5_student_ids);


        // --- Project 6: Prevalence of Dermatosis among Pregnant Women ---
        $p6_students = [
            'Moneeb Montasir Sabeel Adam' => ['email' => 'moneeb.montasir@gmail.com', 'id' => '1711603465'],
            'Monia Ibrahim Osman Ibrahim' => ['email' => 'monia.ibrahim@gmail.com', 'id' => '172000766'],
            'Maha Mohamed Ali Mohamed Ahmed' => ['email' => 'maha.mohamed@gmail.com', 'id' => '11194280'],
        ];
        $p6_student_ids = [];
        foreach ($p6_students as $name => $data) {
            $user = User::create([
                'name' => $name,
                'email' => $data['email'],
                'password' => bcrypt($data['id']),
                'role' => 'graduate',
                'student_id' => $data['id'],
                'department' => 'الطب والجراحة',
                'graduation_year' => 2024,
            ]);
            $p6_student_ids[] = $user->id;
        }

        $pdfProj6 = Project::create([
            'title' => 'Prevalence of Dermatosis among Pregnant Women in White Nile State 2023-2024',
            'description' => "دراسة مقطعية وصفية تهدف لتحديد مدى انتشار وأنماط التغيرات والأمراض الجلدية (Dermatosis) لدى النساء الحوامل في ولاية النيل الأبيض، السودان.\n\nشملت الدراسة 100 امرأة حامل وأظهرت النتائج أن نسبة انتشار التغيرات الجلدية بلغت 69%، وكان العرض الأكثر شيوعاً هو الحكة الجلدية (Itching) بنسبة 23.9%، تليها بقع الظهر (Back spots) بنسبة 22.4%، مع بداية ظهور أغلب هذه التغيرات الجلدية في الثلث الثاني من فترة الحمل (50.7%).",
            'specialty' => 'الطب والجراحة',
            'technologies' => 'SPSS, Questionnaires',
            'grade' => 'A',
            'year' => 2024,
            'graduate_id' => $p6_student_ids[0],
            'supervisor_id' => $profMuhasin->id,
            'file_url' => 'uploads/projects/dermatosis_pregnancy.pdf',
        ]);
        $pdfProj6->students()->sync($p6_student_ids);


        // --- Project 7: Assessment of knowledge and Practice of Nurses and Midwives ---
        $p7_students = [
            'Abrar Ibrahim Ahmed Hassan' => ['email' => 'abrar.ibrahim@gmail.com', 'id' => '25220001'],
            'Aseel Azheri Mohammed Ali Makki' => ['email' => 'aseel.azheri@gmail.com', 'id' => '25220002'],
            'Hala Mohammed Abd Elrahman Ali' => ['email' => 'hala.mohammed@gmail.com', 'id' => '25220003'],
            'Masaged Ragab Idris Omer' => ['email' => 'masaged.ragab@gmail.com', 'id' => '25220004'],
            'Mather Abd Allah Ali Elshambati' => ['email' => 'mather.abdallah@gmail.com', 'id' => '25220005'],
            'Shahinda Adam Ali Omer' => ['email' => 'shahinda.adam@gmail.com', 'id' => '25220006'],
        ];
        $p7_student_ids = [];
        foreach ($p7_students as $name => $data) {
            $user = User::create([
                'name' => $name,
                'email' => $data['email'],
                'password' => bcrypt($data['id']),
                'role' => 'graduate',
                'student_id' => $data['id'],
                'department' => 'علوم التمريض',
                'graduation_year' => 2026,
            ]);
            $p7_student_ids[] = $user->id;
        }

        $pdfProj7 = Project::create([
            'title' => 'Assessment of knowledge and Practice of Nurses and Midwives Regarding Immediate Post Cesarean Care At Sinnar Teaching Hospital',
            'description' => "دراسة وصفية مقطعية لتقييم معارف وممارسات الممرضين والقابلات (30 مشاركاً) فيما يتعلق بالرعاية الفورية بعد الولادة القيصرية في مستشفى سنار التعليمي للتوليد وأمراض النساء.\n\nأظهرت النتائج أن مستوى المعرفة العام كان متوسطاً بنسبة 67% مع أداء مقبول في الرعاية الأساسية الروتينية (مراقبة العلامات الحيوية وإدرار البول وتقييم الجرح)، ولكن كشفت الدراسة عن وجود فجوات معرفية وممارسة سريرية في مجالات متخصصة مثل الآثار الجانبية للأدوية المخدرة والمسكنة، تقييم الألم، البدء المبكر للرضاعة، والوقاية من جلطات الأوردة العميقة (DVT).",
            'specialty' => 'علوم التمريض',
            'technologies' => 'SPSS, Observational Checklist',
            'grade' => 'A',
            'year' => 2026,
            'graduate_id' => $p7_student_ids[0],
            'supervisor_id' => $profFatima->id,
            'file_url' => 'uploads/projects/nurses_post_cesarean_care.pdf',
        ]);
        $pdfProj7->students()->sync($p7_student_ids);


        // 7. Original seeded Questions & Answers
        $q1 = Question::create([
            'student_id' => $student1->id,
            'content' => 'ما هي المتطلبات الأساسية لربط نظام دفع إلكتروني بتطبيق لارافيل؟ وكيف يمكن البدء بذلك؟',
        ]);

        $q2 = Question::create([
            'student_id' => $student2->id,
            'content' => 'هل الأفضل لمشروع التخرج استخدام قاعدة بيانات NoSQL (مثل MongoDB) أم قاعدة بيانات SQL (مثل MySQL) لموقع تجارة إلكترونية؟',
        ]);

        Answer::create([
            'question_id' => $q1->id,
            'user_id' => $grad1->id,
            'content' => "أهلاً عمر، للبدء تحتاج أولاً لاختيار بوابة الدفع (مثل Stripe أو Paytabs). أغلب البوابات توفر حزماً برمجية (SDK) خاصة بلغة PHP ولارافيل.\n\nيمكنك تثبيت حزمة Stripe الرسمية عبر Composer وتخزين مفاتيح الربط (API Keys) في ملف .env، ثم إنشاء Controller يتعامل مع طلبات الدفع وإرجاع حالة العملية للمستخدم. هناك العديد من الشروحات التفصيلية في التوثيق الرسمي لكل بوابة.",
        ]);

        Answer::create([
            'question_id' => $q2->id,
            'user_id' => $prof2->id,
            'content' => "أهلاً ليلى. لموقع تجارة إلكترونية، أنصحك بشدة باستخدام قاعدة بيانات علاقة (SQL) مثل MySQL أو PostgreSQL. التجارة الإلكترونية تتطلب معاملات مالية صارمة (ACID Transactions) وعلاقات وثيقة بين الجداول (مثل علاقة الطلب بالمستخدم والمنتجات).\n\nبينما NoSQL ممتازة للبيانات غير المهيكلة أو التحليلات الضخمة، لكنها قد تسبب مشاكل تماسك البيانات في الأنظمة التجارية الحساسة.",
        ]);
    }
}
