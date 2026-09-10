<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Allergy;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ChronicDisease;
use App\Models\Client;
use App\Models\Consultation;
use App\Models\Meal;
use App\Models\MedicalRecord;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Date Filter Logic
        $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : Carbon::now()->startOfMonth();
        $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to'))->endOfDay() : Carbon::now()->endOfDay();

        // Base Data Bag
        $data = [
            'dateFrom' => $dateFrom->format('Y-m-d'),
            'dateTo' => $dateTo->format('Y-m-d'),
            'user' => $user,
        ];

        // 1. System Admin Stats
        if ($user->hasRole('Admin')) {
            $data = array_merge($data, $this->getAdminStats($dateFrom, $dateTo));
        }

        // 2. Restaurant Manager Stats
        if ($user->hasRole('Restaurant Manager')) {
            $data = array_merge($data, $this->getRestaurantManagerStats($user, $dateFrom, $dateTo));
        }

        // 3. Specialist Stats
        if ($user->hasRole('Specialist') || $user->hasRole('Nutrition Manager')) {
            // Basic Specialist Stats for both roles
            $data = array_merge($data, $this->getSpecialistStats($user, $dateFrom, $dateTo));
        }

        // 4. Nutrition Manager Specific Stats
        if ($user->hasRole('Nutrition Manager')) {
            $data = array_merge($data, $this->getNutritionManagerStats($dateFrom, $dateTo));
        }

        return view('shared.dashboard', $data);
    }

    /**
     * Admin "Eagle Eye" Validated Stats
     */
    private function getAdminStats($from, $to)
    {
        // Total Revenue: Orders (completed) + Consultations (paid/closed?)
        // Assuming 'completed' for Carts and 'closed'/'confirmed' for Consultations
        // Adjust status strings based on actual DB values found during research

        $ordersRevenue = Cart::where('state', 'completed')
            ->whereBetween('date', [$from, $to])
            ->sum('total_price');

        // Consultations Revenue (Check Payment model or Consultation cost)
        // Assuming logic: Consultations have payments or fixed price.
        // Using Payment model if linked, or sum of consultation prices if available.
        // Based on models viewed: Consultation has 'payment_status' but no price column directly visible in scan,
        // but Payment model exists. accurately:
        $consultationsRevenue = \App\Models\Payment::where('status', 'success') // Assuming 'success'
            ->whereBetween('created_at', [$from, $to])
            ->sum('amount');

        $totalRevenue = $ordersRevenue + $consultationsRevenue;

        // User Growth
        $newClients = Client::whereBetween('created_at', [$from, $to])->count();
        // Assuming Restaurants/Nutritionists creation timestamp on their related User or own table
        // Best proxy: User creation date filtered by role
        $newUsers = User::whereBetween('created_at', [$from, $to])->count();

        // Live Activity
        $activeOrders = Cart::whereIn('state', ['pending', 'processing', 'out_for_delivery'])->count();
        $activeConsultations = Consultation::where('status', 'active')
            ->where('end_time', '>', now())
            ->count();

        // Peak Hours (System Wide Orders)
        $peakHours = Cart::select(DB::raw('HOUR(time) as hour'), DB::raw('count(*) as count'))
            ->whereBetween('date', [$from, $to])
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->get();

        // Specialists Performance
        $specialistPerformance = \App\Models\Nutritionist::with(['user'])
            ->withCount(['diets']) // Approximate activity
            ->get()
            ->map(function ($spec) use ($from, $to) {
                // Calculate consultations count dynamically
                $consultationsCount = Consultation::where('nutritionist_id', $spec->nutritionist_id)
                    ->whereBetween('start_time', [$from, $to])
                    ->count();

                // Revenue (This is tricky without direct relationship easily summable, approximation via Payments)
                // Assuming Payment has consultation_id.
                $revenue = \App\Models\Payment::whereHas('consultation', function ($q) use ($spec) {
                    $q->where('nutritionist_id', $spec->nutritionist_id);
                })
                    ->where('status', 'success')
                    ->whereBetween('created_at', [$from, $to])
                    ->sum('amount');

                return [
                    'name' => $spec->user->Fname.' '.$spec->user->Lname,
                    'consultations_count' => $consultationsCount,
                    'revenue' => $revenue,
                ];
            })->sortByDesc('revenue')->take(10);

        // System Health (Failed Jobs)
        $systemErrors = DB::table('failed_jobs')->count();

        return [
            'admin_total_revenue' => $totalRevenue,
            'admin_new_users' => $newUsers,
            'admin_active_orders' => $activeOrders,
            'admin_active_consultations' => $activeConsultations,
            'admin_system_errors' => $systemErrors,
            'admin_peak_hours' => $peakHours,
            'admin_specialists_performance' => $specialistPerformance,
        ];
    }

    /**
     * Restaurant Manager Stats
     */
    private function getRestaurantManagerStats($user, $from, $to)
    {
        // Get restaurants managed by this user
        $restaurantIds = Restaurant::where('manager_id', $user->user_id)->pluck('restaurants_id');

        // Sales Chart (Daily)
        $salesData = Cart::whereIn('restaurants_id', $restaurantIds)
            ->where('state', 'completed')
            ->whereBetween('date', [$from, $to])
            ->select(DB::raw('DATE(date) as date'), DB::raw('sum(total_price) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Order Trends
        $orderTrends = Cart::whereIn('restaurants_id', $restaurantIds)
            ->whereBetween('date', [$from, $to])
            ->select('state', DB::raw('count(*) as count'))
            ->groupBy('state')
            ->get();

        // Top Selling Meals
        $topMeals = CartItem::whereHas('cart', function ($q) use ($restaurantIds, $from, $to) {
            $q->whereIn('restaurants_id', $restaurantIds)
                ->where('state', 'completed')
                ->whereBetween('date', [$from, $to]);
        })
            ->select('meals_id', DB::raw('sum(quantity) as total_qty'))
            ->groupBy('meals_id')
            ->with('meal')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Manager Peak Hours
        $peakHours = Cart::whereIn('restaurants_id', $restaurantIds)
            ->whereBetween('date', [$from, $to])
            ->select(DB::raw('HOUR(time) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour', 'asc') // Order by hour naturally
            ->get();

        return [
            'manager_sales_data' => $salesData,
            'manager_order_trends' => $orderTrends,
            'manager_top_meals' => $topMeals,
            'manager_peak_hours' => $peakHours,
        ];
    }

    /**
     * Specialist Stats
     */
    private function getSpecialistStats($user, $from, $to)
    {
        // Active Consultations
        $activeCount = Consultation::where('nutritionist_id', $user->user_id)
            ->where('status', 'active')
            ->count();

        // Upcoming Appointments
        $upcoming = Consultation::with(['client', 'type'])
            ->where('nutritionist_id', $user->user_id)
            ->where('start_time', '>', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        // Personal Revenue
        $revenue = \App\Models\Payment::whereHas('consultation', function ($q) use ($user) {
            $q->where('nutritionist_id', $user->user_id);
        })
            ->where('status', 'success') // Assuming success status
            ->whereBetween('created_at', [$from, $to])
            ->sum('amount');

        // Client BMI Avg (from BodyData)
        // Access BodyData through Client model (User -> Client?? No, Consultation -> Client(User) -> Client(Model) -> BodyData)
        $clientIds = Consultation::where('nutritionist_id', $user->user_id)
            ->pluck('client_id'); // These are user_ids

        $avgBmi = 0;
        // Calculation: weight / (height/100)^2
        // We can do this in DB or PHP. PHP for safety on zero division.
        $bodyDatas = \App\Models\BodyData::whereIn('clients_id', $clientIds)->get();
        if ($bodyDatas->count() > 0) {
            $totalBmi = 0;
            $count = 0;
            foreach ($bodyDatas as $bd) {
                if ($bd->height_cm > 0 && $bd->weight_kg > 0) {
                    $heightM = $bd->height_cm / 100;
                    $bmi = $bd->weight_kg / ($heightM * $heightM);
                    $totalBmi += $bmi;
                    $count++;
                }
            }
            $avgBmi = $count > 0 ? $totalBmi / $count : 0;
        }

        return [
            'specialist_active_count' => $activeCount,
            'specialist_upcoming' => $upcoming,
            'specialist_revenue' => $revenue,
            'specialist_avg_bmi' => number_format($avgBmi, 1),
        ];
    }

    /**
     * Nutrition Manager Stats
     */
    private function getNutritionManagerStats($from, $to)
    {
        $pendingMeals = Meal::where('state', 'pending')->count();

        // Health Trends
        $topChronic = ChronicDisease::withCount('clients')
            ->orderByDesc('clients_count')
            ->take(5)
            ->get();

        $topAllergies = Allergy::withCount('clients')
            ->orderByDesc('clients_count')
            ->take(5)
            ->get();

        // Top Medications (Medical Records)
        $topMedications = MedicalRecord::withCount('clients')
            ->orderByDesc('clients_count')
            ->take(5)
            ->get();

        // Popular Diets (Based on Client table 'diets_id')
        $dietPopularity = Client::select('diets_id', DB::raw('count(*) as count'))
            ->whereNotNull('diets_id')
            ->groupBy('diets_id')
            ->with('diet')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        return [
            'nutrition_pending_meals' => $pendingMeals,
            'nutrition_top_chronic' => $topChronic,
            'nutrition_top_allergies' => $topAllergies,
            'nutrition_top_medications' => $topMedications,
            'nutrition_diet_popularity' => $dietPopularity,
        ];
    }
}
