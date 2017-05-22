<?php

namespace Gmory\Laranotes\Test\Models;

use Gmory\Laranotes\Traits\NotesTrait;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	use NotesTrait;
	
    protected $table = 'users';

    protected $guarded = [];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}