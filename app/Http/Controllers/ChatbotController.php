<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function query(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = trim($request->input('message'));
        $normalized = $this->normalizeText($message);
        
        $apiKey = env('GEMINI_API_KEY');

        // If Gemini API Key is available, try to use real AI
        if (!empty($apiKey)) {
            try {
                $reply = $this->queryGemini($message, $apiKey);
                if ($reply) {
                    return response()->json([
                        'reply' => $reply
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Gemini API Error: ' . $e->getMessage());
                // Fallback to rule-based on failure
            }
        }

        // ==========================================
        // LOCAL RULE-BASED FALLBACK ENGINE
        // ==========================================

        // 1. GREETINGS
        if (preg_match('/^(مرحبا|اهلان|اهلا|السلام|سلام|صباح|مساء|hello|hi|hey)/ui', $normalized)) {
            $reply = "أهلاً بك في منصة **مركز إرث لمشاريع التخرج**! 👋\n\nأنا مساعدك الذكي، يمكنك سؤالي عن:\n📊 **تحليل المشاريع المضافة** ونسب التخصصات.\n🔮 **التنبؤات التقنية** والتوجهات المستقبلية.\n🔍 **البحث عن مشاريع** معينة (اكتب اسم المشروع أو التقنية).\n👨‍🏫 **المشرفين الأكاديميين** في المنصة.\n⚙️ **طريقة استخدام المنصة** وصلاحيات الحسابات.\n\nكيف يمكنني مساعدتك اليوم؟";
            return response()->json([
                'reply' => $this->appendAdminHint($reply)
            ]);
        }

        // 2. PLATFORM DEVELOPER
        if ($this->containsKeywords($normalized, ['مطور', 'برمج', 'صنع', 'من صمم', 'raheeq', 'رحيق', 'بريد المطور'])) {
            $reply = "🛡️ **معلومات المطور:**\nتم تصميم وتطوير منصة **مركز إرث لمشاريع التخرج** بواسطة المبرمج المتميز:\n\n**الاسم:** Raheeq Hassan 👨‍💻\n**البريد الإلكتروني:** raheegohassan@gmail.com\n**التقنيات المستخدمة:** Laravel 11, SQLite/MySQL, Glassmorphic CSS System.";
            return response()->json([
                'reply' => $this->appendAdminHint($reply)
            ]);
        }

        // 3. ANALYSIS & STATS
        if ($this->containsKeywords($normalized, ['حلل', 'تحليل', 'احصائيات', 'ارقام', 'نسبة', 'اكثر التخصصات', 'اكثر الاقسام', 'احصائيه'])) {
            return response()->json([
                'reply' => $this->appendAdminHint($this->generateAnalysisReply())
            ]);
        }

        // 4. PREDICTIONS & FUTURE TRENDS
        if ($this->containsKeywords($normalized, ['تنبأ', 'توقعات', 'تنبؤ', 'توقع', 'مستقبل', 'مستقبلا', 'قادم', 'مشاريع قادمة', 'تقنيات قادمة', 'ترند'])) {
            return response()->json([
                'reply' => $this->appendAdminHint($this->generatePredictionsReply())
            ]);
        }

        // 5. HOW TO UPLOAD & PERMISSIONS
        if ($this->containsKeywords($normalized, ['كيف ارفع', 'طريقة الرفع', 'رفع مشروع', 'اضافة مشروع', 'اضيف مشروع', 'صلاحية الرفع', 'مين يرفع'])) {
            $reply = "🎓 **صلاحيات رفع مشاريع التخرج:**\n\nبناءً على التحديثات الأمنية والأكاديمية للمنصة:\n* **صلاحية رفع المشاريع حصرية لمدير النظام (Admin) فقط** لضمان دقة المرفقات وصحة البيانات الأكاديمية وكتاب التخرج المرفوع (صيغة PDF).\n* عند قيام المسؤول برفع مشروع، يقوم النظام **تلقائياً بإنشاء حسابات للخريجين المشاركين** (حتى 5 خريجين كحد أقصى) وتوليد بريد إلكتروني لهم مشتق من أسمائهم باللغة العربية بكلمة مرور افتراضية هي `password`.\n* يمكن للطلاب والخريجين والدكاترة تصفح المشاريع والبحث عنها وتحميل كتاب التخرج، وطرح الأسئلة ومناقشتها.";
            return response()->json([
                'reply' => $this->appendAdminHint($reply)
            ]);
        }

        // 6. SEARCH FOR SUPERVISORS
        if ($this->containsKeywords($normalized, ['مشرف', 'مشرفين', 'دكتور', 'دكاتره', 'اساتذه', 'الاستاذ', 'الدكاترة'])) {
            return response()->json([
                'reply' => $this->appendAdminHint($this->generateSupervisorsReply())
            ]);
        }

        // 7. SPECIFIC PROJECT SEARCH (FALLBACK MATCHING)
        $searchReply = $this->searchProjectsReply($message);
        if ($searchReply) {
            return response()->json([
                'reply' => $this->appendAdminHint($searchReply)
            ]);
        }

        // 8. GENERAL FALLBACK
        $reply = "عذراً، لم أفهم سؤالك تماماً. 🧐\n\nيمكنك سؤالي بشكل مباشر مثل:\n* *\"ما هي مشاريع الذكاء الاصطناعي؟\"*\n* *\"حلل لي مشاريع المنصة\"*\n* *\"تنبأ بالتقنيات المستقبلية\"*\n* *\"من هو مطور المنصة؟\"*\n* *\"كيف يتم رفع مشروع؟\"*";
        return response()->json([
            'reply' => $this->appendAdminHint($reply)
        ]);
    }

    private function queryGemini($userMessage, $apiKey)
    {
        // Fetch Live Database Info for Gemini Context
        $totalProjects = Project::count();
        $totalProfessors = User::where('role', 'professor')->count();
        $totalGraduates = User::where('role', 'graduate')->count();
        
        $specialties = Project::distinct()->pluck('specialty')->filter()->implode(', ') ?: 'لا يوجد حالياً';
        $supervisors = User::where('role', 'professor')->take(10)->pluck('name')->implode(', ') ?: 'لا يوجد حالياً';
        $featuredProjects = Project::latest()->take(3)->pluck('title')->implode(', ') ?: 'لا يوجد حالياً';
        
        $techs = Project::pluck('technologies')->filter()->flatMap(function($item) {
            return array_map('trim', explode(',', $item));
        })->unique()->take(10)->implode(', ') ?: 'لا يوجد حالياً';

        // Construct complete system context prompt
        $systemContext = "أنت مساعد ذكي مدمج في منصة 'مركز إرث لمشاريع التخرج' (GP Legacy Hub).\n" .
            "تلتزم بالإجابة باللغة العربية دائماً وبأسلوب ودود وواضح ومحترف وتستخدم التنسيق الغني (Markdown).\n" .
            "مطور المنصة ومبرمجها هو المطور المتميز: Raheeq Hassan (raheegohassan@gmail.com).\n\n" .
            "إليك معلومات حية ومباشرة من قاعدة بيانات المنصة الحالية لمساعدتك في تقديم إجابات دقيقة وصحيحة:\n" .
            "- إجمالي مشاريع التخرج الموثقة بالمنصة: {$totalProjects} مشروع.\n" .
            "- إجمالي الخريجين المسجلين: {$totalGraduates} خريج.\n" .
            "- إجمالي المشرفين الأكاديميين (الأساتذة): {$totalProfessors} مشرف.\n" .
            "- الأقسام والتخصصات المتاحة حالياً: {$specialties}.\n" .
            "- بعض الأساتذة المشرفين المسجلين: {$supervisors}.\n" .
            "- أحدث المشاريع المضافة مؤخراً بالمنصة: {$featuredProjects}.\n" .
            "- التقنيات الأبرز المستخدمة في المشاريع: {$techs}.\n" .
            "- خطط المنصة المستقبلية: نتنبأ بنمو تخصص الذكاء الاصطناعي بنسبة 35%، وتشمل خططنا إدراج محرك توظيف للخريجين، والتحليل التلقائي لكتب المشاريع باستخدام AI.\n\n" .
            "تستطيع الإجابة على أي سؤال يطرحه المستخدم (سواء كان يخص المنصة أو أسئلة علمية، تقنية، أكاديمية، عامة، إلخ) مستعيناً بهذه البيانات إذا لزم الأمر، ولديك الحرية الكاملة في صياغة الإجابة عن أي سؤال عام آخر خارج المنصة بذكاء وعمق.";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $systemContext],
                        ['text' => "سؤال المستخدم: " . $userMessage]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }

        throw new \Exception('Failed response from Gemini API: ' . $response->body());
    }

    private function appendAdminHint($reply)
    {
        // If user is admin and api key is missing, append a hint
        if (auth()->check() && auth()->user()->isAdmin()) {
            $reply .= "\n\n💡 *تلميح للمسؤول (Raheeq Hassan):* لتفعيل محرك الذكاء الاصطناعي الشامل (Gemini AI)، يرجى إضافة مفتاح `GEMINI_API_KEY` في ملف `.env` الخاص بالمنصة لكي يجيب البوت على أي سؤال عام.";
        }
        return $reply;
    }

    private function normalizeText($text)
    {
        $text = mb_strtolower($text);
        $charMap = [
            'أ' => 'ا', 'إ' => 'ا', 'آ' => 'ا',
            'ة' => 'ه', 'ى' => 'ا'
        ];
        return strtr($text, $charMap);
    }

    private function containsKeywords($text, array $keywords)
    {
        foreach ($keywords as $keyword) {
            $normalizedKeyword = $this->normalizeText($keyword);
            if (mb_strpos($text, $normalizedKeyword) !== false) {
                return true;
            }
        }
        return false;
    }

    private function generateAnalysisReply()
    {
        $totalProjects = Project::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalGraduates = User::where('role', 'graduate')->count();
        $totalProfessors = User::where('role', 'professor')->count();

        if ($totalProjects === 0) {
            return "📊 **التحليل الإحصائي للمنصة:**\n\nلا توجد مشاريع مضافة في قاعدة البيانات حالياً لبدء تحليلها. بمجرد إضافة المسؤول لمشاريع التخرج، سأقوم بتقديم إحصائيات متكاملة عن نسب التخصصات والسنوات.";
        }

        // Project specialties distribution
        $specialties = Project::select('specialty', DB::raw('count(*) as count'))
            ->groupBy('specialty')
            ->orderBy('count', 'desc')
            ->get();

        $specialtyStatsText = "";
        foreach ($specialties as $spec) {
            $percentage = round(($spec->count / $totalProjects) * 100, 1);
            $specialtyStatsText .= "🔹 **{$spec->specialty}**: {$spec->count} مشروع ({$percentage}%)\n";
        }

        // Top active supervisors
        $supervisors = Project::select('supervisor_id', DB::raw('count(*) as count'))
            ->with('supervisor')
            ->groupBy('supervisor_id')
            ->orderBy('count', 'desc')
            ->take(3)
            ->get();

        $supervisorStatsText = "";
        foreach ($supervisors as $sup) {
            if ($sup->supervisor) {
                $supervisorStatsText .= "👤 **{$sup->supervisor->title} {$sup->supervisor->name}**: أشرف على {$sup->count} مشروع\n";
            }
        }

        // Unique technologies
        $techs = Project::pluck('technologies')->filter()->flatMap(function($item) {
            return array_map('trim', explode(',', $item));
        })->unique()->filter()->values();
        $totalTechs = $techs->count();
        $topTechs = $techs->take(6)->implode(', ');

        return "📊 **التحليل الإحصائي لمنصة مركز إرث:**\n\n" .
               "📈 **إحصائيات عامة:**\n" .
               "* إجمالي مشاريع التخرج الموثقة: **{$totalProjects} مشاريع**.\n" .
               "* إجمالي الخريجين المسجلين: **{$totalGraduates} خريج**.\n" .
               "* المشرفين الأكاديميين: **{$totalProfessors} مشرف**.\n\n" .
               "🎓 **توزيع المشاريع حسب التخصصات:**\n" .
               $specialtyStatsText . "\n" .
               "👨‍🏫 **أكثر المشرفين نشاطاً:**\n" .
               ($supervisorStatsText ?: "لا يوجد بيانات إشراف حالياً.\n") . "\n" .
               "🛠️ **التقنيات المستخدمة:**\n" .
               "* إجمالي التقنيات الموثقة: **{$totalTechs} تقنية**.\n" .
               "* أبرز التقنيات الحالية: `{$topTechs}`.\n\n" .
               "استنتجت التحليلات أن هناك توجهاً ممتازاً نحو رقمنة المشاريع الأكاديمية وتوفير أرشيف تقني عالي التنظيم.";
    }

    private function generatePredictionsReply()
    {
        $totalProjects = Project::count();

        // Base predictions logic on current database data if available
        $trendTech = "الذكاء الاصطناعي وتعلم الآلة";
        $trendReason = "نظراً للاهتمام العالمي والجامعي المتزايد بحلول الأتمتة والتحليلات الذكية.";

        if ($totalProjects > 0) {
            // Find the most frequent specialty or technology
            $topSpecialty = Project::select('specialty', DB::raw('count(*) as count'))
                ->groupBy('specialty')
                ->orderBy('count', 'desc')
                ->first();
            if ($topSpecialty) {
                $trendTech = $topSpecialty->specialty;
                $trendReason = "نظراً لكونه التخصص الأكثر تسجيلاً للمشاريع في قاعدة بياناتنا حالياً بنسبة نشاط عالية.";
            }
        }

        return "🔮 **التوقعات والتنبؤات التقنية المستقبلية (مركز إرث):**\n\n" .
               "1️⃣ **التخصص التقني الأبرز مستقبلاً:**\n" .
               "نتنبأ بأن تخصص **({$trendTech})** سيظل متصدراً بنسبة نمو متوقعة تصل إلى **35%** في الدفعات القادمة، {$trendReason}\n\n" .
               "2️⃣ **ترند التقنيات المستخدمة (2026 - 2027):**\n" .
               "* **الذكاء الاصطناعي التوليدي:** زيادة متوقعة بنسبة **40%** في دمج نماذج اللغات الكبيرة (LLMs) و Gemini/GPT في التطبيقات الأكاديمية.\n" .
               "* **أطر عمل الويب الحديثة:** ثبات نسبي وتفضيل متزايد لـ `Laravel` و `Python (FastAPI)` كخلفية برمجية، مع `React/Vue` للواجهات.\n" .
               "* **إنترنت الأشياء (IoT):** توجه متزايد نحو مشاريع الأجهزة الذكية والأبحاث الطبية والزراعية.\n\n" .
               "3️⃣ **تنبؤات وتطويرات المنصة القادمة:**\n" .
               "* **مرحلة ربط التوظيف:** نتوقع توفير قسم خاص للشركات في المستقبل لاستكشاف مشاريع التخرج المتميزة وتوظيف خريجيها مباشرة.\n" .
               "* **التحليل التلقائي لكتب المشاريع:** دمج نماذج ذكاء اصطناعي تقوم بقراءة كتب الـ PDF المرفوعة وتوليد ملخصات تلقائية وتصنيف التقنيات تلقائياً دون تدخل يدوي.";
    }

    private function generateSupervisorsReply()
    {
        $professors = User::where('role', 'professor')->get();
        if ($professors->isEmpty()) {
            return "👨‍🏫 **المشرفون الأكاديميون:**\n\nلا يوجد أساتذة مشرفين مسجلين في النظام حالياً.";
        }

        $list = "👨‍🏫 **المشرفون الأكاديميون المسجلون بالمنصة:**\n\n";
        foreach ($professors as $prof) {
            $projectCount = Project::where('supervisor_id', $prof->id)->count();
            $list .= "* **{$prof->title} {$prof->name}** - قسم: **{$prof->department}** | المشاريع المشرف عليها: *{$projectCount}*\n";
        }
        return $list;
    }

    private function searchProjectsReply($queryText)
    {
        // Extract possible project search words
        $cleanQuery = preg_replace('/(ما هي|ماهي|عرض|ابحث عن|ابحث|بحث عن|مشاريع|مشروع|في تخصص|تخصص|تقنية|المستخدمه|المستخدمة|سنة|سنه)/ui', '', $queryText);
        $cleanQuery = trim($cleanQuery);

        if (strlen($cleanQuery) < 2) {
            return null;
        }

        $projects = Project::with('supervisor')
            ->where('title', 'like', "%{$cleanQuery}%")
            ->orWhere('description', 'like', "%{$cleanQuery}%")
            ->orWhere('specialty', 'like', "%{$cleanQuery}%")
            ->orWhere('technologies', 'like', "%{$cleanQuery}%")
            ->orWhere('year', 'like', "%{$cleanQuery}%")
            ->latest()
            ->take(5)
            ->get();

        if ($projects->isEmpty()) {
            return null;
        }

        $reply = "🔍 **عثرت لك على هذه المشاريع المطابقة في قاعدة البيانات:**\n\n";
        foreach ($projects as $project) {
            $supervisorText = $project->supervisor ? "{$project->supervisor->title} {$project->supervisor->name}" : "غير محدد";
            $reply .= "📂 **{$project->title}**\n";
            $reply .= "🎓 تخصص: `{$project->specialty}` | سنة: `{$project->year}`\n";
            $reply .= "👨‍🏫 المشرف: *{$supervisorText}*\n";
            if ($project->technologies) {
                $reply .= "🛠️ التقنيات: `{$project->technologies}`\n";
            }
            $reply .= "🔗 [عرض التفاصيل](".route('projects.show', $project->id).")\n\n";
        }

        return $reply;
    }

    public function predictTrends(Request $request)
    {
        $apiKey = env('GEMINI_API_KEY');
        
        $totalProjects = Project::count();
        $totalProfessors = User::where('role', 'professor')->count();
        $totalGraduates = User::where('role', 'graduate')->count();
        
        $specialties = Project::distinct()->pluck('specialty')->filter()->implode(', ') ?: 'تقنية معلومات, ذكاء اصطناعي, هندسة برمجيات';
        $techs = Project::pluck('technologies')->filter()->flatMap(function($item) {
            return array_map('trim', explode(',', $item));
        })->unique()->take(15)->implode(', ') ?: 'Laravel, React, Node.js, Python, Flutter, TailwindCSS';

        $prompt = "بصفتك خبيراً في تحليل اتجاهات التعليم العالي وسوق العمل التكنولوجي، قم بتحليل قاعدة بيانات مشاريع التخرج الحالية:\n" .
            "- عدد المشاريع الكلية المرفوعة: {$totalProjects}\n" .
            "- التخصصات المتاحة: {$specialties}\n" .
            "- التقنيات المستخدمة حالياً: {$techs}\n\n" .
            "بناءً على هذه البيانات، وبناءً على توجهات سوق العمل العالمي والمحلي الحالي (مثل الثورة في الذكاء الاصطناعي التوليدي، وتطوير الويب السحابي، والتطبيقات المحمولة الهجينة، وإنترنت الأشياء، وحاجة الشركات للمبرمجين):\n" .
            "1. توقع وتنبأ بـ 3 عناوين مشاريع تخرج مميزة ومبتكرة يمكن تقديمها السنة القادمة في هذه التخصصات، واشرح بإيجاز لماذا هي مطلوبة.\n" .
            "2. ما هي 3 تقنيات وأدوات برمجية يُتوقع أن يزداد الطلب عليها واستعمالها بشكل كبير جداً في المشاريع القادمة وتعتبر متوافقة مع سوق العمل؟\n" .
            "3. ما هي 3 تقنيات أو أساليب يُنصح بعدم استخدامها أو يُتوقع تراجع الطلب عليها لعدم جدواها في سوق العمل حالياً؟\n\n" .
            "قم بصياغة الرد باللغة العربية بتنسيق Markdown جذاب ومقروء جداً ومنظم باستخدام الأيقونات التعبيرية.";

        if (!empty($apiKey)) {
            try {
                $reply = $this->queryGeminiDirect($prompt, $apiKey);
                if ($reply) {
                    return response()->json(['success' => true, 'prediction' => $reply]);
                }
            } catch (\Exception $e) {
                Log::error('Gemini Prediction API Error: ' . $e->getMessage());
            }
        }

        // Local Rule-Based Fallback Prediction
        $fallback = "### 🔮 التوقعات الذكية لمشاريع التخرج والتقنيات (السنة القادمة)\n\nبناءً على التخصصات النشطة حالياً في المنصة واحتياجات سوق العمل التكنولوجي لعام 2026:\n\n#### 1. مشاريع التخرج المقترحة والمطلوبة:\n* **نظام التشخيص الطبي المعتمد على الذكاء الاصطناعي:** تدمج رؤية الكمبيوتر (Computer Vision) لتسهيل الكشف المبكر عن الأمراض الفيروسية.\n* **منصة التوظيف الذكي للخريجين:** ربط مباشر لخريجي الكلية مع الشركات المحلية وتطبيق خوارزميات مطابقة المهارات مع الوظائف الشاغرة.\n* **نظام إدارة الشبكات الذكية وإنترنت الأشياء:** لإدارة الطاقة والموارد في المباني الجامعية الذكية.\n\n#### 2. تقنيات يُتوقع زيادة الطلب عليها (موصى بها):\n* **Next.js & TailwindCSS:** لبناء واجهات ويب سريعة جداً ومتوافقة مع محركات البحث.\n* **Python (FastAPI & PyTorch):** لتطوير نماذج ذكاء اصطناعي خفيفة وسهلة الدمج عبر الـ APIs.\n* **Flutter:** لتطوير تطبيقات الهاتف المتعددة المنصات بكود برمجى واحد وتوفير الوقت.\n\n#### 3. تقنيات وتوجهات يُنصح بتجنبها أو يقل الطلب عليها:\n* **PHP Native (بدون إطار عمل):** لصعوبة صيانة الكود وتأمين الثغرات الأمنية مقارنة بـ Laravel.\n* **jQuery:** تراجع دورها بشكل كبير لصالح أطر العمل التفاعلية الحديثة مثل React و Vue.js.\n* **قواعد البيانات التقليدية الضخمة للمشاريع البسيطة:** يفضل استخدام SQLite للمشاريع المتوسطة أو MongoDB للبيانات غير المهيكلة لسرعة التطوير.";

        return response()->json(['success' => true, 'prediction' => $fallback]);
    }

    private function queryGeminiDirect($prompt, $apiKey)
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }

        return null;
    }
}
