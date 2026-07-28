<?php

// تحديد مساحة الاسم (Namespace) الخاص بالنماذج البرمجية لتنظيم الأكواد
namespace App\Models;

// استيراد نموذج Eloquent الأساسي من إطار العمل لارافل للتعامل مع قاعدة البيانات
use Illuminate\Database\Eloquent\Model;
// استيراد سمة HasFactory لتسهيل التفاعل مع بيانات الاختبار والمصانع
use Illuminate\Database\Eloquent\Factories\HasFactory;
// استيراد السمة Fillable لتحديد الحقول التي يمكن حفظها دفعة واحدة
use Illuminate\Database\Eloquent\Attributes\Fillable;

// تحديد الحقول المسموح بكتابتها وحفظها بشكل مباشر (Mass Assignment) في جدول المشاريع
#[Fillable(['title', 'description', 'specialty', 'technologies', 'file_url', 'year', 'graduate_id', 'supervisor_id', 'grade'])]
// تعريف كلاس المشروع الموروث من نموذج Eloquent الأساسي
class Project extends Model
{
    // استخدام السمة المحددة للمصانع داخل الكلاس لتسهيل إنشاء البيانات الوهمية
    use HasFactory;

    // علاقة المشروع بالخريج الأساسي الذي قام برفعه أو تسجيله كمالك أول
    public function graduate()
    {
        // علاقة BelongsTo: المشروع ينتمي إلى خريج واحد (مستخدم) بالربط مع graduate_id
        return $this->belongsTo(User::class, 'graduate_id');
    }

    // علاقة المشروع بالأستاذ المشرف الأكاديمي على البحث
    public function supervisor()
    {
        // علاقة BelongsTo: المشروع ينتمي ويشرف عليه أستاذ واحد (مستخدم) بالربط مع supervisor_id
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    // علاقة المشروع بجميع الطلاب المشاركين فيه (علاقة Many-to-Many بين المشاريع والطلاب)
    public function students()
    {
        // علاقة Many-to-Many: المشروع يشترك فيه عدة طلاب عبر جدول project_user مع التواقيت
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')->withTimestamps();
    }
}
