<?php

namespace Zhihe\MoneySystem\Listener;

use Flarum\Discussion\Discussion;
use Flarum\Http\RequestUtil;
use Illuminate\Contracts\Events\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Zhihe\MoneySystem\Event\DiscussionWasViewed;

class EmitDiscussionViewedEvent
{
    /**
     * Called using the ApiController::prepareDataForSerialization extender.
     */
    public static function handle($controller, $data, ServerRequestInterface $request = null): void
    {
        if ($data instanceof Discussion && $request) {
            $actor = RequestUtil::getActor($request);
            
            // Check access before emitting view event
            if ($actor) {
                static::checkDiscussionAccess($actor, $data);
            }
            
            // Only emit event for logged-in users
            if ($actor && !$actor->isGuest()) {
                resolve(Dispatcher::class)->dispatch(
                    new DiscussionWasViewed($data, $actor)
                );
            }
        }
    }
    
    private static function checkDiscussionAccess($actor, $discussion)
    {
        // Skip money checks for guests (controlled at higher level)
        if ($actor->isGuest()) {
            return;
        }
        
        // Allow admins/moderators to bypass payment requirements
        if ($actor->can('discussion.viewWithoutPayment')) {
            return;
        }
        
        // Allow users to view their own discussions
        if ($discussion->user_id === $actor->id) {
            return;
        }
        
        // Check if user has minimum balance required to view new discussions
        $settings = resolve(\Flarum\Settings\SettingsRepositoryInterface::class);
        $minimumBalance = (float) $settings->get('zhihe-money-system.minimum_balance', 0);
        
        if ($actor->money >= $minimumBalance) {
            return;
        }
        
        // User has insufficient balance - check if they've viewed this discussion before
        $hasViewedBefore = $discussion->readers()
            ->where('user_id', $actor->id)
            ->whereNotNull('last_read_at')
            ->exists();
            
        if ($hasViewedBefore) {
            return;
        }
        
        // User has insufficient balance and hasn't viewed this discussion before
        throw new \Flarum\Foundation\ValidationException([
            'message' => 'You need at least ' . number_format($minimumBalance) . ' money to view new discussions. Please comment or create discussions to earn money.'
        ]);
    }
}