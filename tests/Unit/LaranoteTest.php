<?php 

namespace Gmory\Laranotes\Test;

use Gmory\Laranotes\Exceptions\CannotDeleteOldWithoutFirstSettingAttachTo;
use Gmory\Laranotes\Exceptions\NoteRequiresSomethingToAttachTo;
use Gmory\Laranotes\Laranote;
use Gmory\Laranotes\Models\Note;
use Gmory\Laranotes\Test\Models\Post;
use Gmory\Laranotes\Test\Models\User;
use Gmory\Laranotes\Test\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LaranoteTest extends TestCase
{
    use DatabaseMigrations;

	protected $post;
	protected $user;
	protected $note;

	public function setUp()
    {
        parent::setUp();
        $this->seedModels();

        $this->post = Post::first();
        $this->user = User::first();
        $this->laranote = new Laranote;
    }

	/** @test */
    public function can_be_attached_to_a_model()
    {
    	$note = $this->laranote->attach($this->user)->note('Test Note');

        $this->assertEquals($this->user->notes()->first(), $note->fresh());
    }

    /** @test */
    public function can_regard_another_model()
    {
        $note = $this->laranote->attach($this->user)->regarding($this->post)->note('Test Note');

        $this->assertEquals($this->post->regardedBy()->first(), $note->fresh());
    }

    /** @test */
    public function can_delete_all_prior_notes_on_an_attached_to_model()
    {
        $this->laranote->attach($this->user)->note('Test Note 1');
        $this->laranote->attach($this->user)->note('Test Note 2');
        $this->laranote->attach($this->user)->note('Test Note 3');
        $this->assertEquals(3, $this->user->notes()->count());

        $this->laranote->deleteOld($this->user);

        $this->assertEquals(0, $this->user->fresh()->notes()->count());

        $this->laranote->regarding($this->post)->note('Test Note 1');
        $this->laranote->regarding($this->post)->note('Test Note 2');
        $this->laranote->regarding($this->post)->note('Test Note 3');
        $this->assertEquals(3, $this->post->regardedBy()->count());

        $this->laranote->deleteOld(null, $this->post);

        $this->assertEquals(0, $this->post->fresh()->regardedBy()->count());
    }
    /** @test */
    public function can_delete_all_prior_notes_on_a_regarded_model()
    {
        $this->laranote->attach($this->user)->regarding($this->post)->note('Test Note 1');
        $this->laranote->attach($this->user)->regarding($this->post)->note('Test Note 2');
        $this->laranote->attach($this->user)->regarding($this->post)->note('Test Note 3');
        $this->assertEquals(3, $this->post->regardedBy()->count());

        $this->laranote->deleteOld(null, $this->post);

        $this->assertEquals(0, $this->post->fresh()->regardedBy()->count());
    }
    /** @test */
    public function can_delete_only_prior_notes_that_belong_to_both_specified_attach_and_regard_models()
    {
        $anotherUser = User::create(['name' => 'Jane Smith', 'email' => 'janesmith@test.com', 'password' => 'qwerty']);
        $anotherPost = Post::create(['title' => 'Second Post']);

        $this->laranote->attach($this->user)->regarding($this->post)->note('Test Note 1');
        $this->laranote->attach($this->user)->regarding($this->post)->note('Test Note 2');
        $this->laranote->attach($this->user)->regarding($this->post)->note('Test Note 3');
        $this->assertEquals(3, $this->user->notes()->count());
        $this->assertEquals(3, $this->post->regardedBy()->count());

        $this->laranote->attach($anotherUser)->regarding($this->post)->note('Test Note 1');
        $this->assertEquals(4, $this->post->regardedBy()->count());

        $this->laranote->attach($this->user)->regarding($anotherPost)->note('Test Note 1');
        $this->assertEquals(4, $this->user->notes()->count());

        $this->laranote->deleteOld($this->user, $this->post, true);

        $this->assertEquals(1, $this->user->fresh()->notes()->count());
        $this->assertEquals(1, $this->post->fresh()->regardedBy()->count());
    }
    /** @test */
    public function can_delete_prior_notes_based_on_content()
    {
        $anotherUser = User::create(['name' => 'Jane Smith', 'email' => 'janesmith@test.com', 'password' => 'qwerty']);

        $noteOne = $this->laranote->attach($this->user)->regarding($this->post)->note('Test Note 1');
        $noteTwo = $this->laranote->attach($this->user)->regarding($this->post)->note('Test Note 2');
        $noteThree = $this->laranote->attach($anotherUser)->regarding($this->post)->note('Test Note 3');
        $noteFour = $this->laranote->attach($anotherUser)->regarding($this->post)->note('Test Note 4');
        $this->assertEquals(2, $this->user->fresh()->notes()->count());
        $this->assertEquals(4, $this->post->fresh()->regardedBy()->count());

        $this->laranote->deleteOld($this->user, null, false, 'Test Note 1');

        $this->assertEquals(1, $this->user->fresh()->notes()->count());
        $this->assertEquals(3, $this->post->fresh()->regardedBy()->count());
    }
    /** @test */
    public function requires_a_model_to_attach_to()
    {
        try {
            $this->laranote->note('Test Note 1');
        } catch (NoteRequiresSomethingToAttachTo $e) {
            $this->assertEquals(0, $this->user->notes()->count());
            return;
        }
        $this->fail('Note was created without a model to attach to');
    }

    /** @test */
    public function requires_attach_to_prior_to_delete_old()
    {
        $this->laranote->attach($this->user)->note('Test Note 1');
        $this->assertEquals(1, $this->user->notes()->count());

        // Reset
        $this->laranote = new Laranote;

        try {
            $this->laranote->deleteOld()->note('Test Note 2');
        } catch (CannotDeleteOldWithoutFirstSettingAttachTo $e) {
            $this->assertEquals(1, $this->user->notes()->count());
            $this->assertEquals('Test Note 1', $this->user->notes()->first()->content);
            return;
        }
        $this->fail('deleteOld was called without anything set as attachedTo yet');
    }

    /** @test */
    public function can_ensure_a_note_is_unique_before_adding_it_to_a_model()
    {
        // Gets created
        $noteOne = $this->laranote->attach($this->user)->note('Test Note 1', true);
        $this->assertEquals(1, $this->user->notes()->count());

        // Fails
        $noteTwo = $this->laranote->attach($this->user)->note('Test Note 1', true); 
        $this->assertEquals(1, $this->user->notes()->count());

        // Gets created
        $noteThree = $this->laranote->attach($this->user)->regarding($this->post)->note('Test Note 1', true); 
        $this->assertEquals(2, $this->user->notes()->count());

        // Fails
        $noteFour = $this->laranote->attach($this->user)->regarding($this->post)->note('Test Note 1', true); 
        $this->assertEquals(2, $this->user->notes()->count());

        // Gets created
        $noteFive = $this->laranote->attach($this->user)->regarding($this->post)->note('Test Note 2', true); 
        $this->assertEquals(3, $this->user->notes()->count());

        // Gets created
        $noteFive = $this->laranote->attach($this->user)->regarding($this->post)->note('Test Note 2'); 
        $this->assertEquals(4, $this->user->notes()->count());
    }

    protected function seedModels()
    {
        User::create(['name' => 'John Doe', 'email' => 'johndoe@test.com', 'password' => 'qwerty']);
        Post::create(['title' => 'Test Post']);
    }
}