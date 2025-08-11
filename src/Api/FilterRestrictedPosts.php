<?php

namespace Zhihe\MoneySystem\Api;

use Flarum\Api\Serializer\PostSerializer;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;

class FilterRestrictedPosts
{
    /**
     * Filter restricted post content based on user's money balance
     */
    public static function filterContent(PostSerializer $serializer, Post $post, array $attributes): array
    {
        $actor = $serializer->getActor();
        
        // Skip filtering for guests
        if ($actor->isGuest()) {
            return $attributes;
        }
        
        // Skip if post is not restricted
        if (!$post->is_restricted) {
            return $attributes;
        }
        
        // Allow admins/moderators to bypass payment requirements
        if ($actor->can('discussion.viewWithoutPayment')) {
            return $attributes;
        }
        
        // Allow discussion authors to see all restricted posts in their own discussions
        if ($post->discussion && $post->discussion->user_id === $actor->id) {
            return $attributes;
        }
        
        // Check user's money against restricted posts minimum
        $settings = resolve(SettingsRepositoryInterface::class);
        $restrictedMinimum = (float) $settings->get('zhihe-money-system.restricted_posts_minimum_money', 50);
        
        // If minimum is 0, restriction is disabled
        if ($restrictedMinimum <= 0) {
            return $attributes;
        }
        
        // If user has insufficient funds, replace content with placeholder
        if ($actor->money < $restrictedMinimum) {
            $translator = resolve('translator');
            $placeholderText = $translator->trans(
                'zhihe-money-system.forum.restricted_content.placeholder',
                ['amount' => number_format($restrictedMinimum)]
            );
            
            // Replace content with placeholder message
            $attributes['contentHtml'] = '<div class="Post-restrictedContent">' .
                '<i class="fas fa-lock"></i> ' .
                $placeholderText .
                '</div>';
            
            // Also clear the plain content to prevent any leaks
            if (isset($attributes['content'])) {
                $attributes['content'] = strip_tags($placeholderText);
            }
        }
        
        return $attributes;
    }
}