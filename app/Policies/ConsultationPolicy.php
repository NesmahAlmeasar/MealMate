<?php

namespace App\Policies;

use App\Models\Consultation;
use App\Models\User;

class ConsultationPolicy
{
    /**
     * Determine whether the user can view the consultation.
     */
    public function view(User $user, Consultation $consultation)
    {
        return $user->user_id == $consultation->client_id
            || $user->user_id == $consultation->nutritionist_id
            || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the consultation.
     */
    public function update(User $user, Consultation $consultation)
    {
        return $user->user_id == $consultation->nutritionist_id
            || $user->hasRole('admin');
    }
}
