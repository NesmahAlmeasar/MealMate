<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\Meal;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create a notification.
     */
    public function createNotification(int $userId, string $type, string $title, string $message, ?array $data = null, ?string $scheduledFor = null)
    {
        try {
            return Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'scheduled_for' => $scheduledFor,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create notification: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Notify all nutrition specialists when a meal is added.
     */
    public function notifyNutritionSpecialists(Meal $meal)
    {
        // Find all users with role 'Specialist' or equivalent if defined
        // Assuming we check roles via hasRole method or query
        // Or we can find all nutritionists from Nutritionist model if linked to user

        // For this implementation, I'll assume we look for users with 'specialist' role
        $specialists = User::whereHas('roles', function ($q) {
            $q->where('name', 'Specialist');
        })->get();

        foreach ($specialists as $specialist) {
            $this->createNotification(
                $specialist->user_id,
                'meal_added',
                'New Meal Added',
                "A new meal '{$meal->name}' has been added and requires review.",
                ['meal_id' => $meal->meals_id] // Assuming meal primary key is id or meal_id
            );
        }
    }

    /**
     * Notify specialist of new consultation booking.
     */
    public function notifySpecialistOfBooking(Consultation $consultation)
    {
        $this->createNotification(
            $consultation->nutritionist_id,
            'consultation_booked',
            'حجز استشارة جديدة',
            'لديك استشارة جديدة من '.($consultation->client ? $consultation->client->Fname : 'Client'),
            ['consultation_id' => $consultation->consultation_id]
        );
    }

    public function notifyClientOfStart(Consultation $consultation)
    {
        $this->createNotification(
            $consultation->client_id,
            'consultation_started',
            'بدأت الاستشارة',
            'تم تفعيل استشارتك مع '.($consultation->nutritionist ? $consultation->nutritionist->Fname : 'Specialist'),
            ['consultation_id' => $consultation->consultation_id]
        );
    }

    public function notifyConsultationClosed(Consultation $consultation)
    {
        // Client
        $this->createNotification(
            $consultation->client_id,
            'consultation_closed',
            'انتهت الاستشارة',
            'انتهت مدة استشارتك مع '.($consultation->nutritionist ? $consultation->nutritionist->Fname : 'Specialist'),
            ['consultation_id' => $consultation->consultation_id]
        );

        // Specialist
        $this->createNotification(
            $consultation->nutritionist_id,
            'consultation_closed',
            'انتهت الاستشارة',
            'انتهت مدة الاستشارة مع '.($consultation->client ? $consultation->client->Fname : 'Client'),
            ['consultation_id' => $consultation->consultation_id]
        );
    }

    /**
     * Notify restaurant manager of new order.
     *
     * @param  \App\Models\Cart  $order
     */
    public function notifyRestaurantManager($order)
    {
        // Find restaurant manager
        $restaurant = $order->restaurant;
        if ($restaurant && $restaurant->manager_id) {
            $this->createNotification(
                $restaurant->manager_id,
                'order_submitted',
                'New Order Received',
                "New order #{$order->cart_id} has been submitted.",
                ['order_id' => $order->cart_id]
            );
        }
    }

    /**
     * Notify user and restaurant manager of order status change.
     *
     * @param  \App\Models\Cart  $order
     */
    public function notifyOrderStatusChange($order, string $oldStatus, string $newStatus)
    {
        // Notify User (Client)
        // Cart relates to client -> user.
        $userId = $order->client ? $order->client->clients_id : ($order->clients_id ?? null);

        if ($userId) {
            $this->createNotification(
                $userId,
                'order_status_changed',
                'Order Status Updated',
                "Your order #{$order->cart_id} status has changed to {$newStatus}.",
                ['order_id' => $order->cart_id, 'old_status' => $oldStatus, 'new_status' => $newStatus]
            );
        }

        // Notify Manager
        $restaurant = $order->restaurant;
        if ($restaurant && $restaurant->manager_id) {
            $this->createNotification(
                $restaurant->manager_id,
                'order_status_changed',
                'Order Status Updated',
                "Order #{$order->cart_id} status updated to {$newStatus}.",
                ['order_id' => $order->cart_id, 'new_status' => $newStatus]
            );
        }
    }

    /**
     * Notify receiver when a new chat message is sent.
     */
    public function notifyNewMessage(Message $message)
    {
        $this->createNotification(
            $message->receiver_id,
            'new_message',
            'New Message',
            'You have a new message from '.($message->sender ? $message->sender->Fname : 'User'),
            ['message_id' => $message->id, 'sender_id' => $message->sender_id]
        );
    }

    /**
     * Create custom appointment reminder.
     */
    public function notifyAppointmentReminder(int $userId, string $title, string $message, string $reminderDateTime)
    {
        return $this->createNotification(
            $userId,
            'appointment_reminder',
            $title,
            $message,
            null,
            $reminderDateTime
        );
    }

    /**
     * Create custom user reminder.
     */
    public function createCustomReminder(int $userId, string $title, string $message, string $reminderDateTime)
    {
        return $this->notifyAppointmentReminder($userId, $title, $message, $reminderDateTime);
    }
}
