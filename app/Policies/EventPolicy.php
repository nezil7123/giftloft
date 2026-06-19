<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Only the owner may view the management view of an event.
     */
    public function view(User $user, Event $event): bool
    {
        return $user->id === $event->user_id;
    }

    /**
     * Any authenticated user may create an event.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only the owner may update the event.
     */
    public function update(User $user, Event $event): bool
    {
        return $user->id === $event->user_id;
    }

    /**
     * Only the owner may delete the event.
     */
    public function delete(User $user, Event $event): bool
    {
        return $user->id === $event->user_id;
    }
}
