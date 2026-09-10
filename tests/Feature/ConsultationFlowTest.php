<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Consultation;
use App\Models\ConsultationType;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationFlowTest extends TestCase
{
    use RefreshDatabase; // Use if DB resets are okay, otherwise avoid

    protected function setUp(): void
    {
        parent::setUp();
        // Seed Types if needed or create them
        ConsultationType::firstOrCreate(['name' => 'تشخيص'], ['price' => 2000, 'duration_days' => 2]);
        ConsultationType::firstOrCreate(['name' => 'متابعة'], ['price' => 3000, 'duration_days' => 7]);
        ConsultationType::firstOrCreate(['name' => 'عودة'], ['price' => 5000, 'duration_days' => 3]);

        // Create Role
        \App\Models\Role::firstOrCreate(['name' => 'Specialist'], ['description' => 'Specialist']);
    }

    public function test_client_can_initiate_consultation()
    {
        $this->withoutExceptionHandling();
        $client = User::factory()->create();
        $nutritionist = User::factory()->create();
        $nutritionist->roles()->attach(\App\Models\Role::where('name', 'Specialist')->first());

        // Client profile created by User observer
        \App\Models\Nutritionist::create(['nutritionist_id' => $nutritionist->user_id, 'Academic_level' => 'PhD', 'description' => 'Test']);

        $this->mock(\App\Services\BasGatewayService::class, function ($mock) {
            $mock->shouldReceive('initiateTransaction')->andReturn('TEST_TOKEN');
        });

        $this->actingAs($client);

        $response = $this->postJson('/api/consultations/start', [
            'nutritionist_id' => $nutritionist->user_id,
            'type_id' => 1, // Diagnosis
            // 'client_complaint' => 'Headache', // Removed
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('consultations', [
            'client_id' => $client->user_id,
            'status' => 'pending_payment',
        ]);
    }

    public function test_payment_confirmation_activates_consultation()
    {
        $client = User::factory()->create();
        // Client created by Observer

        $this->actingAs($client);

        $nutritionist = User::factory()->create();
        \App\Models\Nutritionist::create(['nutritionist_id' => $nutritionist->user_id, 'Academic_level' => 'PhD', 'description' => 'Test']);

        $type = ConsultationType::first();

        // Create pending
        $consultation = Consultation::create([
            'client_id' => $client->user_id,
            'nutritionist_id' => $nutritionist->user_id,
            'type_id' => $type->type_id,
            'status' => 'pending_payment',
            'payment_status' => 'pending',
        ]);

        $payment = Payment::create([
            'consultation_id' => $consultation->consultation_id,
            'amount' => $type->price,
            'currency' => 'YER',
            'status' => 'pending',
            'token' => 'TOK123',
        ]);

        // Mock confirmation
        $this->mock(\App\Services\BasGatewayService::class, function ($mock) {
            $mock->shouldReceive('checkTransactionStatus')->andReturn(['trxStatus' => 'processed', 'trxId' => 'TXN_TEST']);
        });

        $response = $this->postJson('/api/consultations/confirm', [
            'payment_id' => $payment->payment_id,
            'gateway_response' => json_encode(['data' => ['trxId' => 'TXN_TEST', 'trxStatus' => 'processed']]),
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('consultations', [
            'consultation_id' => $consultation->consultation_id,
            'status' => 'active',
        ]);
    }

    public function test_chat_status_check()
    {
        $client = User::factory()->create();
        // Client created by Observer

        $nutritionist = User::factory()->create();
        \App\Models\Nutritionist::create(['nutritionist_id' => $nutritionist->user_id, 'Academic_level' => 'PhD', 'description' => 'Test']);

        $this->actingAs($client);

        // Case 1: No active -> false
        $response = $this->getJson("/api/chat/can-send/{$nutritionist->user_id}");
        $response->assertJson(['can_send' => false]);

        // Case 2: Active
        $consultation = Consultation::create([
            'client_id' => $client->user_id,
            'nutritionist_id' => $nutritionist->user_id,
            'type_id' => 1,
            'status' => 'active',
            'start_time' => now(),
            'end_time' => now()->addDays(2),
        ]);

        $response = $this->getJson("/api/chat/can-send/{$nutritionist->user_id}");
        $response->assertJson(['can_send' => true]);
    }
}
