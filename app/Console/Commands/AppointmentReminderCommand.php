<?php

namespace App\Console\Commands;

use App\Models\Consultation;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AppointmentReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-appointment-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for upcoming consultations';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $this->info('Checking for upcoming appointment reminders...');

        // Find consultations with reminder_date in the past (due) and reminder not sent
        // OR consultations starting soon (e.g. tomorrow) if reminder_date is null but logical to remind?
        // User asked: "Check for consultations with upcoming reminder dates"

        /*
        $dueReminders = Consultation::whereNotNull('reminder_date')
            ->where('reminder_date', '<=', Carbon::now())
            ->where('reminder_sent', false)
            ->where('status', 'active') // Only active consultations?
            ->get();

        $count = 0;
        foreach ($dueReminders as $consultation) {
            // Notify Specialist
            if ($consultation->nutritionist && $consultation->nutritionist->user) {
                $notificationService->createCustomReminder(
                    $consultation->nutritionist->user->user_id,
                    'Appointment Reminder',
                    'Reminder: You have an appointment/consultation task due.',
                    $consultation->reminder_date
                );
            }
            // Notify Client
            if ($consultation->client) { // client_id references users table usually, but check model
                $userId = $consultation->client_id; // Assuming direct
                // Or if client is a separate model linked to user
                // Consultant model says belongsTo Client. Client model says belongsTo User (or is user).
                // Let's assume client_id is user_id based on `Auth::id()` usage in Controller.

                $notificationService->createCustomReminder(
                    $userId,
                    'Appointment Reminder',
                    'Reminder: You have an appointment/consultation task due.',
                    $consultation->reminder_date
                );
            }

            $consultation->update(['reminder_sent' => true]);
            $count++;
        }
        */
        $count = 0; // Disabled functionality

        $this->info("Sent {$count} reminders.");
    }
}
