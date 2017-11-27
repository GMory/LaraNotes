<?php 

namespace Gmory\Laranotes\Test;

use Gmory\Laranotes\Laranote;
use Gmory\Laranotes\LaranotesFacade;
use Gmory\Laranotes\Test\TestCase;

class FacadeTest extends TestCase
{
	/** @test */
	function returns_correct_object()
	{
		$laranote = new Laranote;
	    $facade = LaranotesFacade::getFacadeRoot();
	    $this->assertEquals($laranote, $facade);
	}
}