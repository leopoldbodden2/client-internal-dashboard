<?php

namespace App\Policies;

use App\User;
use App\CallTrackingValue;
use Illuminate\Auth\Access\HandlesAuthorization;

class CallTrackingValuePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the call tracking value.
     *
     * @param  \App\User  $user
     * @param  \App\CallTrackingValue  $callTrackingValue
     * @return mixed
     */
    public function view(User $user, CallTrackingValue $callTrackingValue)
    {
        return $user->id === $callTrackingValue->user_id;
    }

    /**
     * Determine whether the user can create call tracking values.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasCtm();
    }

    /**
     * Determine whether the user can update the call tracking value.
     *
     * @param  \App\User  $user
     * @param  \App\CallTrackingValue  $callTrackingValue
     * @return mixed
     */
    public function update(User $user, CallTrackingValue $callTrackingValue)
    {
        return $user->id === $callTrackingValue->user_id;
    }

    /**
     * Determine whether the user can delete the call tracking value.
     *
     * @param  \App\User  $user
     * @param  \App\CallTrackingValue  $callTrackingValue
     * @return mixed
     */
    public function delete(User $user, CallTrackingValue $callTrackingValue)
    {
        //
    }

    /**
     * Determine whether the user can restore the call tracking value.
     *
     * @param  \App\User  $user
     * @param  \App\CallTrackingValue  $callTrackingValue
     * @return mixed
     */
    public function restore(User $user, CallTrackingValue $callTrackingValue)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the call tracking value.
     *
     * @param  \App\User  $user
     * @param  \App\CallTrackingValue  $callTrackingValue
     * @return mixed
     */
    public function forceDelete(User $user, CallTrackingValue $callTrackingValue)
    {
        //
    }
}
