<?php 

namespace Gmory\Laranotes\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
	protected $table = 'laranotes';

    public $guarded = [];

    public function noting()
    {
        return $this->morphTo();
    }
    
    public function regarding()
    {
        return $this->morphTo();
    }
}