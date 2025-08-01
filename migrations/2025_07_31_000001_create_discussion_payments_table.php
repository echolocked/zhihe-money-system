<?php

use Illuminate\Database\Schema\Blueprint;
use Flarum\Database\Migration;

return Migration::createTable(
    'zhihe_discussion_payments',
    function (Blueprint $table) {
        $table->increments('id');
        $table->unsignedInteger('discussion_id');
        $table->unsignedInteger('user_id');
        $table->integer('amount');
        $table->dateTime('payment_time');
        
        $table->foreign('discussion_id')->references('id')->on('discussions')->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        
        // Prevent duplicate payments for same discussion by same user
        $table->unique(['discussion_id', 'user_id']);
        
        $table->index(['user_id', 'discussion_id']);
    }
);