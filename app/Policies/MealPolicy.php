<?php

namespace App\Policies;

use App\Models\Meal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MealPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any meals.
     */
    public function viewAny(User $user)
    {
        // Admin and Restaurant Manager can view meals
        return $user->hasRole('Admin') || $user->hasRole('Restaurant Manager');
    }

    /**
     * Determine whether the user can view the meal.
     */
    public function view(User $user, Meal $meal)
    {
        // Admin can view all meals
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Restaurant Manager can only view meals from their restaurant
        if ($user->hasRole('Restaurant Manager')) {
            $managedRestaurant = $user->managedRestaurant;

            if ($managedRestaurant && $meal->restaurant_id === $managedRestaurant->restaurants_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create meals.
     */
    public function create(User $user)
    {
        // Admin and Restaurant Manager can create meals
        return $user->hasRole('Admin') || $user->hasRole('Restaurant Manager');
    }

    /**
     * Determine whether the user can update the meal.
     */
    public function update(User $user, Meal $meal)
    {
        // Admin can edit all meals
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Restaurant Manager can only edit meals from their restaurant
        if ($user->hasRole('Restaurant Manager')) {
            // Get the restaurant managed by this user
            $managedRestaurant = $user->managedRestaurant;

            if ($managedRestaurant && $meal->restaurant_id === $managedRestaurant->restaurants_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the meal.
     */
    public function delete(User $user, Meal $meal)
    {
        // Admin can delete all meals
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Restaurant Manager can only delete meals from their restaurant
        if ($user->hasRole('Restaurant Manager')) {
            $managedRestaurant = $user->managedRestaurant;

            if ($managedRestaurant && $meal->restaurant_id === $managedRestaurant->restaurants_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can restore the meal.
     */
    public function restore(User $user, Meal $meal)
    {
        // Only Admin can restore meals
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the meal.
     */
    public function forceDelete(User $user, Meal $meal)
    {
        // Only Admin can force delete meals
        return $user->hasRole('Admin');
    }
}
