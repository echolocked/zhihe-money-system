<?php

namespace Zhihe\MoneySystem\Listener;

use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Database\ConnectionInterface;
use Zhihe\MoneySystem\Event\DiscussionWasViewed;

class DeductMoneyOnView
{
    protected $settings;
    protected $db;
    
    public function __construct(SettingsRepositoryInterface $settings, ConnectionInterface $db)
    {
        $this->settings = $settings;
        $this->db = $db;
    }
    
    public function handle(DiscussionWasViewed $event)
    {
        $user = $event->actor;
        $discussion = $event->discussion;
        
        // Skip for guests
        if ($user->isGuest()) {
            return;
        }
        
        // Skip for admins/moderators (they bypass payment)
        if ($user->can('discussion.viewWithoutPayment')) {
            return;
        }
        
        // Check if this is the first time viewing this discussion
        $hasViewedBefore = $discussion->readers()
            ->where('user_id', $user->id)
            ->whereNotNull('last_read_at')
            ->exists();
            
        // Only deduct money for first-time views
        if ($hasViewedBefore) {
            return;
        }
        
        // Get payment amount from settings
        $amount = (int) $this->settings->get('zhihe-money-system.payment_amount', 1);
        
        // Skip if payment amount is 0 (disabled)
        if ($amount <= 0) {
            return;
        }
        
        // Skip if user is viewing their own discussion
        if ($discussion->user_id === $user->id) {
            return;
        }
        
        try {
            // Use transaction to prevent race conditions
            $this->db->transaction(function () use ($user, $amount) {
                // Refresh user to get latest balance
                $user->refresh();
                
                // Deduct money (no validation - access control is handled by policy)
                $user->money -= $amount;
                $user->save();
            });
        } catch (\Exception $e) {
            // Log errors but don't block access (fail open)
            \Log::error('Failed to deduct money for discussion view', [
                'user_id' => $user->id,
                'discussion_id' => $discussion->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}