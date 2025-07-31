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

use Flarum\Extend;
use Flarum\Discussion\Event\Viewing as DiscussionViewing;
use Flarum\Post\Event\Viewing as PostViewing;
use Flarum\User\User;
use Flarum\Discussion\Discussion;
use Flarum\Post\Post;
use Flarum\Tags\Tag;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js')
        ->css(__DIR__ . '/less/admin.less'),

    new Extend\Locales(__DIR__ . '/locale'),

    // Event listeners for access control
    (new Extend\Event())
        ->listen(PostViewing::class, Listener\PostViewingListener::class)
        ->listen(DiscussionViewing::class, Listener\DiscussionViewingListener::class),

    // Database models and relationships
    (new Extend\Model(User::class))
        ->hasMany('moneyTransactions', Models\MoneyTransaction::class, 'user_id'),

    (new Extend\Model(Discussion::class))
        ->hasMany('viewCosts', Models\ViewCost::class, 'discussion_id'),

    (new Extend\Model(Post::class)) 
        ->hasMany('viewCosts', Models\ViewCost::class, 'post_id'),

    (new Extend\Model(Tag::class))
        ->hasMany('moneyRequirements', Models\TagMoneyRequirement::class, 'tag_id'),

    // API routes
    (new Extend\Routes('api'))
        ->post('/money-system/deduct-view-cost', 'money-system.deduct-view-cost', Api\Controller\DeductViewCostController::class)
        ->get('/money-system/check-access', 'money-system.check-access', Api\Controller\CheckAccessController::class)
        ->post('/money-system/tag-requirements', 'money-system.tag-requirements', Api\Controller\TagRequirementsController::class),

    // Middleware for access control
    (new Extend\Middleware('forum'))
        ->add(Middleware\MoneyAccessMiddleware::class),

    // Policies for access control
    (new Extend\Policy())
        ->modelPolicy(Discussion::class, Access\DiscussionPolicy::class)
        ->modelPolicy(Post::class, Access\PostPolicy::class)
        ->modelPolicy(Tag::class, Access\TagPolicy::class),
];