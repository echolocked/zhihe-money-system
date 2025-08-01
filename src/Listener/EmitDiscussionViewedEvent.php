<?php

namespace Zhihe\MoneySystem\Listener;

use Illuminate\Contracts\Events\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Zhihe\MoneySystem\Event\DiscussionWasViewed;

class EmitDiscussionViewedEvent
{
    protected $events;
    
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }
    
    public function __invoke($controller, $data, ServerRequestInterface $request, $document)
    {
        $actor = $request->getAttribute('actor');
        
        // Only emit event for logged-in users
        if ($actor && !$actor->isGuest()) {
            $this->events->dispatch(
                new DiscussionWasViewed($data, $actor)
            );
        }
    }
}