<?php
require_once 'Base.php';

class BaseTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		parent::setUp();
		
		$this->nc = new \decr_jp\NiftyCloud\Base();
	}

	public function tearDown()
	{
		parent::tearDown();
		
		$this->nc = null;
	}
	
	/**
	 * @test
	 */
	public function testバージョン番号が正しいこと()
	{
	  $this->assertEquals('2.0a', $this->nc->version());
	}
}
