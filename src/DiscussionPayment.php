<?php

namespace Zhihe\MoneySystem;

use Flarum\Database\AbstractModel;
use Flarum\Discussion\Discussion;
use Flarum\User\User;

class DiscussionPayment extends AbstractModel
{
    protected $table = 'zhihe_discussion_payments';
    protected $dates = ['payment_time'];
    public $timestamps = false;
    
    protected $fillable = [
        'discussion_id',
        'user_id', 
        'amount',
        'payment_time'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }
    
    public static function hasPaid(int $userId, int $discussionId): bool
    {
        return static::where('user_id', $userId)
            ->where('discussion_id', $discussionId)
            ->exists();
    }
}