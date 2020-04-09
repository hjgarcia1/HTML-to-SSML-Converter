<?php

namespace App;

/**
 * Class Ssml
 * @package App
 */
class Ssml extends \Eloquent
{
    protected $table = 'ssmls';

    protected $fillable = [
        'title',
        'link',
        'content'
    ];
}
