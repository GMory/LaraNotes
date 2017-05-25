<?php 

namespace Gmory\Laranotes;

use Gmory\Laranotes\Exceptions\MustSpecifyBothAttachedAndRegardedModels;
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
     * Delete all past notes associated with the specified models.
     * 
     * @param \Illuminate\Database\Eloquent\Model $attachToModel
     * @param \Illuminate\Database\Eloquent\Model $regardingModel
     * @param boolean $onlyThoseBelongingToBoth
     * @param string $content
     *
     * @throws Gmory\Laranotes\Exceptions\MustSpecifyBothAttachedAndRegardedModels
     *
     * @return $this
     */
    public function deleteOld($attachToModel = null, $regardingModel = null, $onlyThoseBelongingToBoth = false, $content = null)
    {
        if($onlyThoseBelongingToBoth) {

            if (!$attachToModel || !$regardingModel) {
                throw new MustSpecifyBothAttachedAndRegardedModels;
            }

            return $this->deleteQuery($attachToModel->notes()->where('regarding_id', $regardingModel->id), $content);
        }

        if ($attachToModel) {
            $this->deleteQuery($attachToModel->notes(), $content);
        }

        if ($regardingModel) {
            $this->deleteQuery($regardingModel->regardedBy(), $content);
        }

        return $this;
    }

    /**
     * Create the note itself.
     *
     * @param string $content
     * @param boolean $unique
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

        // Work around since whereHas currently doesn't work for polyMorphics (https://github.com/laravel/framework/issues/5429)
        $existingNotes = Note::where('content', $note->content)->get()->filter(function ($value, $key) use ($note) {

            if ($value->noting->id != $note->noting->id) {
                return false;
            }

            if ($note->regarding && (!$value->regarding || ($value->regarding->id != $note->regarding->id))) {
                return false;
            }

            return true;
        });
        
        return ($existingNotes->count() > 0);
    }

    /**
     * Continue a query and rune delete at the end.
     *
     * @param \Illuminate\Database\Eloquent\Relations\MorphMany $query
     * @param string $content
     *
     * @return $this
     */
    private function deleteQuery($query, $content = null) {
        if($content) {
            $query->where('content', $content);
        }

        $query->delete();

        return $this;
    }
}