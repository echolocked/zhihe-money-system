<?php

/*
 * This file is part of zhihe/money-system.
 *
 * Copyright (c) 2025 Zhihe Team.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Zhihe\MoneySystem;

use Flarum\Api\Controller\ShowDiscussionController;
use Flarum\Api\Serializer\PostSerializer;
use Flarum\Discussion\Discussion;
use Flarum\Extend;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),

    new Extend\Locales(__DIR__ . '/locale'),

    // Settings
    (new Extend\Settings())
        ->serializeToForum('zhihe-money-system.payment_amount', 'zhihe-money-system.payment_amount', 'intval')
        ->serializeToForum('zhihe-money-system.minimum_balance', 'zhihe-money-system.minimum_balance', 'floatval')
        ->serializeToForum('zhihe-money-system.restricted_posts_minimum_money', 'zhihe-money-system.restricted_posts_minimum_money', 'floatval'),

    // Event listeners
    (new Extend\Event())
        ->listen(Event\DiscussionWasViewed::class, Listener\DeductMoneyOnView::class)
        ->listen(\Flarum\User\Event\Registered::class, [Listeners\GiveInitialMoney::class, 'handle']),

    // Controller integration to emit events and check access
    (new Extend\ApiController(ShowDiscussionController::class))
        ->prepareDataForSerialization([Listener\EmitDiscussionViewedEvent::class, 'handle']),

    // Filter restricted post content based on user money
    (new Extend\ApiSerializer(PostSerializer::class))
        ->attributes([Api\FilterRestrictedPosts::class, 'filterContent']),
];