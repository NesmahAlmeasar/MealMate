<?php

namespace App\Console\Commands;

use App\Models\Consultation;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CloseExpiredConsultations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consultations:close-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close consultations that have passed their end time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expired = Consultation::where('status', 'active')
            ->where('end_time', '<', now())
            ->get();

        foreach ($expired as $consultation) {
            $consultation->update(['status' => 'completed']);

            // Send Notification
            try {
                app(NotificationService::class)->notifyConsultationClosed($consultation);
            } catch (\Exception $e) {
                $this->error("Failed to notify for consultation {$consultation->consultation_id}");
            }

            $this->info("Closed consultation ID: {$consultation->consultation_id}");
        }

        $this->info("Closed {$expired->count()} expired consultations.");
    }
}
