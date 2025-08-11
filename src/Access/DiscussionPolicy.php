<?php

namespace Zhihe\MoneySystem\Access;

use Flarum\Discussion\Discussion;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

class DiscussionPolicy extends AbstractPolicy
{
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function view(User $actor, Discussion $discussion)
    {
        // TEMPORARY TEST: Always deny access for test3 to see if policy works
        if (!$actor->isGuest() && $actor->username === 'test3') {
            return $this->deny();
        }
        
        // Skip money checks for guests (they don't have money system)
        if ($actor->isGuest()) {
            // Let higher level global permission system handle guest access
            return;
        }
        
        // Allow admins/moderators to bypass payment requirements
        if ($actor->can('discussion.viewWithoutPayment')) {
            return $this->allow();
        }
        
        // Check if user has minimum balance required to view discussions
        $minimumBalance = (float) $this->settings->get('zhihe-money-system.minimum_balance', 0);
        
        if ($actor->money < $minimumBalance) {
            return $this->deny();
        }
        
        // Note: We don't check payment status here - that's handled by the event listener
        // This policy only checks if the user is eligible to view (has enough money)
        // Restricted post content filtering will be handled at the post level
        return $this->allow();
    }
}