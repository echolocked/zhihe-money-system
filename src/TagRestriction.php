<?php

namespace Zhihe\MoneySystem;

use Flarum\Database\AbstractModel;
use Flarum\Tags\Tag;

class TagRestriction extends AbstractModel
{
    protected $table = 'zhihe_tag_restrictions';
    public $timestamps = false;
    
    protected $fillable = [
        'tag_id',
        'minimum_money'
    ];
    
    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
    
    public static function getMinimumMoneyForTags($tagIds): int
    {
        if (empty($tagIds)) {
            return 0;
        }
        
        return static::whereIn('tag_id', $tagIds)
            ->max('minimum_money') ?? 0;
    }
}