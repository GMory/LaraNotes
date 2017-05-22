<?php

namespace Gmory\Laranotes\Test\Models;

use Gmory\Laranotes\Traits\NotesTrait;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
	use NotesTrait;
	
    protected $table = 'posts';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}