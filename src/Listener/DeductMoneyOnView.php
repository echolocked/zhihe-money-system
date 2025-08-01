<?php

namespace Zhihe\MoneySystem\Listener;

use Flarum\Foundation\ValidationException;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Carbon;
use Zhihe\MoneySystem\DiscussionPayment;
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
        
        // Check if already paid
        if (DiscussionPayment::hasPaid($user->id, $discussion->id)) {
            return;
        }
        
        // Get payment amount from settings
        $amount = (int) $this->settings->get('zhihe-money-system.payment_amount', 1);
        
        // Skip if payment amount is 0 (disabled)
        if ($amount <= 0) {
            return;
        }
        
        try {
            // Use transaction to prevent race conditions
            $this->db->transaction(function () use ($user, $discussion, $amount) {
                // Refresh user to get latest balance
                $user->refresh();
                
                // Check if user has enough money
                if ($user->money < $amount) {
                    throw new ValidationException([
                        'money' => 'Insufficient funds to view this discussion'
                    ]);
                }
                
                // Deduct money
                $user->money -= $amount;
                $user->save();
                
                // Record payment
                DiscussionPayment::create([
                    'user_id' => $user->id,
                    'discussion_id' => $discussion->id,
                    'amount' => $amount,
                    'payment_time' => Carbon::now()
                ]);
            });
        } catch (ValidationException $e) {
            // Re-throw validation exceptions (insufficient funds)
            throw $e;
        } catch (\Exception $e) {
            // Log other errors but don't block access (fail open)
            \Log::error('Failed to deduct money for discussion view', [
                'user_id' => $user->id,
                'discussion_id' => $discussion->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}