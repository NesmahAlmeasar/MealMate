<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Services\AccountingService;

class AccountingAndPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected AccountingService $accounting;
    protected int $adminUserId;
    protected int $restaurantManagerUserId;
    protected int $clientUserId;
    protected int $nutritionistUserId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accounting = new AccountingService();

        $this->adminUserId = DB::table('users')->insertGetId([
            'Fname' => 'Admin',
            'Lname' => 'Test',
            'email' => 'admin@test.com',
            'phone' => '0500000001',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->restaurantManagerUserId = DB::table('users')->insertGetId([
            'Fname' => 'Restaurant',
            'Lname' => 'Manager',
            'email' => 'manager@test.com',
            'phone' => '0500000002',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->clientUserId = DB::table('users')->insertGetId([
            'Fname' => 'Client',
            'Lname' => 'Test',
            'email' => 'client@test.com',
            'phone' => '0500000003',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->nutritionistUserId = DB::table('users')->insertGetId([
            'Fname' => 'Nutritionist',
            'Lname' => 'Test',
            'email' => 'nutritionist@test.com',
            'phone' => '0500000004',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** @test */
    public function it_creates_wallet_on_first_access()
    {
        $wallet = $this->accounting->getOrCreateWallet($this->clientUserId);
        $this->assertNotNull($wallet);
        $this->assertEquals($this->clientUserId, $wallet->user_id);
        $this->assertEquals(0.00, (float) $wallet->balance);
        $this->assertDatabaseHas('wallets', ['user_id' => $this->clientUserId, 'balance' => 0.00]);
    }

    /** @test */
    public function it_returns_existing_wallet_on_second_access()
    {
        $this->accounting->getOrCreateWallet($this->clientUserId);
        DB::table('wallets')->where('user_id', $this->clientUserId)->update(['balance' => 150.00]);
        $wallet2 = $this->accounting->getOrCreateWallet($this->clientUserId);
        $this->assertEquals(150.00, (float) $wallet2->balance);
        $count = DB::table('wallets')->where('user_id', $this->clientUserId)->count();
        $this->assertEquals(1, $count);
    }

    /** @test */
    public function it_distributes_cart_payment_with_commission_correctly()
    {
        $restaurantId = DB::table('restaurants')->insertGetId([
            'name' => 'Test Restaurant',
            'manager_id' => $this->restaurantManagerUserId,
            'commission_rate' => 10.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cartId = DB::table('cart')->insertGetId([
            'restaurants_id' => $restaurantId,
            'clients_id' => $this->clientUserId,
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
            'state' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payment = (object) [
            'payable_type' => 'CART',
            'payable_id' => $cartId,
            'clients_id' => $this->clientUserId,
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
            'amount' => '100.00',
            'transaction_id' => 'TX-TEST-CART-001',
        ];

        $this->accounting->recordPaymentSuccess($payment);

        $restaurantWallet = DB::table('wallets')->where('user_id', $this->restaurantManagerUserId)->first();
        $adminWallet = DB::table('wallets')->where('user_id', $this->adminUserId)->first();

        $this->assertEquals(90.00, (float) $restaurantWallet->balance);
        $this->assertEquals(10.00, (float) $adminWallet->balance);
        $this->assertDatabaseHas('ledger_entries', ['transaction_ref' => 'TX-TEST-CART-001', 'entry_type' => 'MEAL_COMMISSION']);
        $this->assertDatabaseHas('cart', ['cart_id' => $cartId, 'state' => 'paid']);
    }

    /** @test */
    public function it_applies_correct_commission_per_restaurant_rate()
    {
        $restaurantId = DB::table('restaurants')->insertGetId([
            'name' => 'Premium Restaurant',
            'manager_id' => $this->restaurantManagerUserId,
            'commission_rate' => 15.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $cartId = DB::table('cart')->insertGetId([
            'restaurants_id' => $restaurantId,
            'clients_id' => $this->clientUserId,
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
            'state' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $payment = (object) [
            'payable_type' => 'CART',
            'payable_id' => $cartId,
            'clients_id' => $this->clientUserId,
            'date' => now()->toDateString(),
            'amount' => '200.00',
            'transaction_id' => 'TX-TEST-CART-002',
        ];
        $this->accounting->recordPaymentSuccess($payment);
        $restaurantWallet = DB::table('wallets')->where('user_id', $this->restaurantManagerUserId)->first();
        $adminWallet = DB::table('wallets')->where('user_id', $this->adminUserId)->first();
        $this->assertEquals(170.00, (float) $restaurantWallet->balance);
        $this->assertEquals(30.00, (float) $adminWallet->balance);
    }

    /** @test */
    public function it_credits_wallet_on_wallet_refill_payment()
    {
        $payment = (object) [
            'payable_type' => 'WALLET_REFILL',
            'payable_id' => $this->clientUserId,
            'user_id' => $this->clientUserId,
            'amount' => '50.00',
            'transaction_id' => 'TX-TEST-REFILL-001',
        ];
        $this->accounting->recordPaymentSuccess($payment);
        $wallet = DB::table('wallets')->where('user_id', $this->clientUserId)->first();
        $this->assertEquals(50.00, (float) $wallet->balance);
        $this->assertDatabaseHas('ledger_entries', ['transaction_ref' => 'TX-TEST-REFILL-001', 'entry_type' => 'WALLET_REFILL']);
    }

    /** @test */
    public function it_creates_payout_request_and_moves_balance_to_pending()
    {
        DB::table('wallets')->insert([
            'user_id' => $this->nutritionistUserId,
            'balance' => 200.00,
            'total_earned' => 200.00,
            'total_withdrawn' => 0.00,
            'pending_withdrawal' => 0.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $result = $this->accounting->requestPayout($this->nutritionistUserId, 150.00, 'الراجحي', '12345678901234');
        $this->assertTrue($result['success']);
        $wallet = DB::table('wallets')->where('user_id', $this->nutritionistUserId)->first();
        $this->assertEquals(50.00, (float) $wallet->balance);
        $this->assertEquals(150.00, (float) $wallet->pending_withdrawal);
        $this->assertDatabaseHas('payout_requests', ['user_id' => $this->nutritionistUserId, 'amount' => 150.00, 'status' => 'pending']);
    }

    /** @test */
    public function it_rejects_payout_when_balance_is_insufficient()
    {
        DB::table('wallets')->insert([
            'user_id' => $this->nutritionistUserId,
            'balance' => 50.00,
            'total_earned' => 50.00,
            'total_withdrawn' => 0.00,
            'pending_withdrawal' => 0.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $result = $this->accounting->requestPayout($this->nutritionistUserId, 200.00, null, null);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('غير كافٍ', $result['message']);
        $this->assertDatabaseMissing('payout_requests', ['user_id' => $this->nutritionistUserId]);
    }

    /** @test */
    public function it_applies_penalty_deduction_from_user_wallet()
    {
        DB::table('wallets')->insert([
            'user_id' => $this->restaurantManagerUserId,
            'balance' => 100.00,
            'total_earned' => 100.00,
            'total_withdrawn' => 0.00,
            'pending_withdrawal' => 0.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $success = $this->accounting->applyPenaltyDeduction(
            $this->restaurantManagerUserId,
            20.00,
            'شكوى عدم جودة الوجبة',
            $this->adminUserId
        );
        $this->assertTrue($success);
        $restaurantWallet = DB::table('wallets')->where('user_id', $this->restaurantManagerUserId)->first();
        $this->assertEquals(80.00, (float) $restaurantWallet->balance);
        $this->assertDatabaseHas('ledger_entries', ['entry_type' => 'PENALTY_DEDUCTION', 'amount' => 20.00]);
    }

    /** @test */
    public function it_requires_auth_to_initiate_checkout()
    {
        $response = $this->postJson('/api/payments/checkout', [
            'payable_type' => 'CART',
            'payable_id' => 1,
            'amount' => 100,
        ]);
        $response->assertStatus(401);
    }

    /** @test */
    public function it_requires_auth_to_view_wallet()
    {
        $response = $this->getJson('/api/wallet');
        $response->assertStatus(401);
    }

    /** @test */
    public function it_handles_bas_webhook_without_crashing()
    {
        $response = $this->postJson('/api/payments/bas/webhook', [
            'transaction_id' => 'TX-BAS-MOCK-9999',
            'status' => 'SUCCESS',
            'amount' => '100.00',
        ]);
        $this->assertNotEquals(500, $response->status());
    }

    /** @test */
    public function it_checks_payment_status_endpoint_exists()
    {
        $response = $this->getJson('/api/payments/status/NON-EXISTENT-TX');
        $this->assertContains($response->status(), [200, 404]);
    }
}
