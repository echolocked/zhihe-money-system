<?php

namespace Zhihe\MoneySystem\Access;

use Flarum\Discussion\Discussion;
use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;
use Zhihe\MoneySystem\TagRestriction;

class DiscussionPolicy extends AbstractPolicy
{
    public function view(User $actor, Discussion $discussion)
    {
        // Skip checks for guests (they can't have money anyway)
        if ($actor->isGuest()) {
            return $this->deny();
        }
        
        // Allow admins/moderators to bypass payment requirements
        if ($actor->can('discussion.viewWithoutPayment')) {
            return $this->allow();
        }
        
        // Check if user has minimum balance (must be >= 0 to view any discussion)
        if ($actor->money < 0) {
            return $this->deny();
        }
        
        // Check tag restrictions if discussion has tags
        $tagIds = $discussion->tags->pluck('id')->toArray();
        if (!empty($tagIds)) {
            $minimumRequired = TagRestriction::getMinimumMoneyForTags($tagIds);
            
            if ($minimumRequired > 0 && $actor->money < $minimumRequired) {
                return $this->deny();
            }
        }
        
        // Note: We don't check payment status here - that's handled by the event listener
        // This policy only checks if the user is eligible to view (has enough money)
        return $this->allow();
    }
}