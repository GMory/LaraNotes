<?php

namespace Gmory\Laranotes\Traits;

use Gmory\Laranotes\Models\Note;

trait NotesTrait {

	public function notes()
    {
        return $this->morphMany(Note::class, 'noting');
    }
    
    public function regardedBy()
    {
        return $this->morphMany(Note::class, 'regarding');
    }

}