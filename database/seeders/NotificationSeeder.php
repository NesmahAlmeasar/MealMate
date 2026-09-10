<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please create users first.');

            return;
        }

        $this->command->info('Creating test notifications...');

        foreach ($users as $user) {
            // Create 3-5 notifications per user
            $notificationCount = rand(3, 5);

            for ($i = 0; $i < $notificationCount; $i++) {
                $types = [
                    [
                        'type' => 'meal_added',
                        'title' => 'وجبة جديدة متاحة',
                        'message' => 'تمت إضافة وجبة "سلطة الكينوا" إلى القائمة',
                    ],
                    [
                        'type' => 'consultation_booked',
                        'title' => 'حجز استشارة جديدة',
                        'message' => 'لديك استشارة جديدة مع أحمد محمد',
                    ],
                    [
                        'type' => 'order_submitted',
                        'title' => 'طلب جديد',
                        'message' => 'تم استلام طلب رقم #'.rand(1000, 9999),
                    ],
                    [
                        'type' => 'order_status_changed',
                        'title' => 'تحديث حالة الطلب',
                        'message' => 'طلبك رقم #'.rand(1000, 9999).' قيد التحضير',
                    ],
                    [
                        'type' => 'new_message',
                        'title' => 'رسالة جديدة',
                        'message' => 'لديك رسالة جديدة من فاطمة علي',
                    ],
                    [
                        'type' => 'appointment_reminder',
                        'title' => 'تذكير بموعد',
                        'message' => 'لديك موعد غداً الساعة 10:00 صباحاً',
                    ],
                    [
                        'type' => 'diet_assigned',
                        'title' => 'حمية جديدة',
                        'message' => 'تم تعيين حمية "نظام البحر المتوسط" لك',
                    ],
                ];

                $randomNotif = $types[array_rand($types)];
                $isRead = rand(0, 1) === 1; // 50% chance of being read

                Notification::create([
                    'user_id' => $user->user_id,
                    'type' => $randomNotif['type'],
                    'title' => $randomNotif['title'],
                    'message' => $randomNotif['message'],
                    'data' => ['test' => true],
                    'is_read' => $isRead,
                    'read_at' => $isRead ? Carbon::now()->subHours(rand(1, 24)) : null,
                    'created_at' => Carbon::now()->subDays(rand(0, 7)),
                ]);
            }
        }

        $totalCreated = Notification::count();
        $this->command->info("✅ Created {$totalCreated} test notifications successfully!");
    }
}
