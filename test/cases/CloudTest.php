<?php
require_once 'Base.php';
class CloudTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		parent::setUp();

		$settings = include('settings.php');
		$this->nc = new \decr_jp\NiftyCloud\Cloud($settings);
	}

	public function tearDown()
	{
		parent::tearDown();
		
		$this->nc = null;
	}
	
	/**
	 * @test
	 */
	public function testインスタンス一覧が取得できること()
	{
	  $results = $this->nc->describe_instances();
	}
}
