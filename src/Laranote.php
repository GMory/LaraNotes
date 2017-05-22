<?php 

namespace Gmory\Laranotes;

use Gmory\Laranotes\Models\Note;
use Illuminate\Database\Eloquent\Model;

class Laranote
{

    protected $attachTo;
    protected $regarding;

    /**
     * Attach note to a model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function attach(Model $model)
    {
        $this->attachTo = $model;
        return $this;
    }

    /**
     * Make note regard another model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function regarding(Model $model)
    {
    	$this->regarding = $model;
        return $this;
    }

    /**
     * Delete all past notes associated with .
     *
     * @param boolean
     */
    public function deleteOld()
    {
        if ($this->attachTo) {
            $this->attachTo->notes()->delete();
        }
        return $this;
    }

    /**
     * Create the note itself.
     *
     * @param string $content
     */
    public function note(string $content)
    {
        $note = new Note;
        $note->content = $content;
        
        if ($this->attachTo) {
            $note->noting()->associate($this->attachTo);
        }

        if ($this->regarding) {
            $note->regarding()->associate($this->regarding);
        }
        
        $note->save();

        return $note;
    }
}