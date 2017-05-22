<?php 

namespace Gmory\Laranotes\Test;

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
    public function can_create_a_note()
    {
        $note = $this->laranote->note('Test Note');
        $this->assertEquals('Test Note', Note::all()->last()->content);
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
        $note = $this->laranote->regarding($this->post)->note('Test Note');

        $this->assertEquals($this->post->regardedBy()->first(), $note->fresh());
    }

    /** @test */
    public function can_delete_all_prior_notes_on_a_model()
    {
        $this->laranote->attach($this->user)->note('Test Note 1');
        $this->laranote->attach($this->user)->note('Test Note 2');
        $this->laranote->attach($this->user)->note('Test Note 3');
        $this->assertEquals(3, $this->user->notes()->count());

        $this->laranote->attach($this->user)->deleteOld()->note('Test Note 4');

        $this->assertEquals(1, $this->user->notes()->count());
    }
    protected function seedModels()
    {
        User::create(['name' => 'John Doe', 'email' => 'johndoe@test.com']);
        Post::create(['title' => 'Test Post']);
    }
}