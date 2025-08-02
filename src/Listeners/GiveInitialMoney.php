<?php

namespace Zhihe\MoneySystem\Listeners;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Event\Registered;
use Flarum\User\User;

class GiveInitialMoney
{
    protected SettingsRepositoryInterface $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function handle(Registered $event): void
    {
        $initialAmount = (float) $this->settings->get('zhihe-money-system.initial_money', 0);
        
        if ($initialAmount > 0) {
            $user = $event->user;
            $user->money = $initialAmount;
            $user->save();
        }
    }
}