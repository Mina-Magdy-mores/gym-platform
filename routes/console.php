<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// 1. أمر تفعيل الاشتراكات المؤجلة يومياً منتصف الليل الساعة 00:05
Schedule::command('subscription:activate-queued')->dailyAt('00:05');

// 2. أمر تنبيهات مواعيد الجلسات الآلية كل 10 دقائق
Schedule::command('subscription:send-booking-reminders')->everyTenMinutes();

// 3. أمر التقرير المالي والإداري الأسبوعي للأدمن صباح كل إثنين الساعة 08:00
Schedule::command('admin:send-weekly-digest')->weeklyOn(1, '08:00');

// 4. أمر فحص وإرسال تنبيهات قرب انتهاء الاشتراكات يومياً الساعة 09:00 صباحاً
Schedule::command('subscription:check-expiring')->dailyAt('09:00');