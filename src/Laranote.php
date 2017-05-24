<?php 

namespace Gmory\Laranotes;

use Gmory\Laranotes\Exceptions\CannotDeleteOldWithoutFirstSettingAttachTo;
use Gmory\Laranotes\Exceptions\NoteRequiresSomethingToAttachTo;
use Gmory\Laranotes\Models\Note;
use Illuminate\Database\Eloquent\Model;

class Laranote
{

    protected $attachTo;
    protected $regarding;
    protected $note;

    /**
     * Attach note to a model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return $this
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
     *
     * @return $this
     */
    public function regarding(Model $model)
    {
    	$this->regarding = $model;
        return $this;
    }

    /**
     * Delete all past notes associated with the attached model.
     *
     * @return $this
     */
    public function deleteOld()
    {
        if (!$this->attachTo) {
            throw new CannotDeleteOldWithoutFirstSettingAttachTo;
        }

        $this->attachTo->notes()->delete();

        return $this;
    }

    /**
     * Create the note itself.
     *
     * @param string $content
     *
     * @return null|Gmory\Laranotes\Models\Note
     */
    public function note(string $content, $unique = false)
    {
        $this->note = new Note;

        $this->attachRelationships();

        $this->note->content = $content;

        if($unique && $this->alreadyCreated()) {
            return;
        }

        $this->note->save();

        return $this->note;
    }

    /**
     * Attach relationships to the note.
     *
     * @throws Gmory\Laranotes\Exceptions\NoteRequiresSomethingToAttachTo
     */
    private function attachRelationships()
    {
        if ($this->regarding) {
            $this->note->regarding()->associate($this->regarding);
        }

        if (!$this->attachTo) {
            throw new NoteRequiresSomethingToAttachTo;
        }

        $this->note->noting()->associate($this->attachTo);
    }

    /**
     * Check if an identical note has already been created.
     *
     * @return boolean
     */
    private function alreadyCreated()
    {
        $note = $this->note;

        $existingNotes = Note::with('regarding')->whereHas('noting', function ($q) use ($note) {
            $q->where('id', $note->noting->id);
        })->where('content', $note->content)->get();

        if($existingNotes->count() == 0) {
            return false;
        }

        if(!$note->regarding) {
            return true;
        }

        foreach($existingNotes as $existingNote) {
            if($existingNote->regarding && ($existingNote->regarding->id == $note->regarding->id)) {
                return true;
            }
        }
        
        return false;
    }
}