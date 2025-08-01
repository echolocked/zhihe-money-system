<?php

use Illuminate\Database\Schema\Blueprint;
use Flarum\Database\Migration;

return Migration::createTable(
    'zhihe_tag_restrictions',
    function (Blueprint $table) {
        $table->increments('id');
        $table->unsignedInteger('tag_id');
        $table->integer('minimum_money');
        
        $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        
        // One restriction per tag
        $table->unique('tag_id');
    }
);