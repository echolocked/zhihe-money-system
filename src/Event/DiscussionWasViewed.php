<?php

namespace Zhihe\MoneySystem\Event;

use Flarum\Discussion\Discussion;
use Flarum\User\User;

class DiscussionWasViewed
{
    public $discussion;
    public $actor;
    
    public function __construct(Discussion $discussion, User $actor)
    {
        $this->discussion = $discussion;
        $this->actor = $actor;
    }
}