<?php
namespace Pofig;


use PHPUnit\Framework\TestCase;


class SanityTest extends TestCase
{
	public function test_BaseCase()
	{
		$subject = new Config();
		$setup = $subject->setup();
		$setup->addStandardLoaders('ini');
		
		$setup->group('dev')
			->addIncludePath(__DIR__ . '/SanityTest/BasicCase/DevConfig');
		
		$setup->group('prod')
			->addIncludePath(__DIR__ . '/SanityTest/BasicCase/ProdConfig');
		
		$result = $subject->get('main/mysql');
		
		self::assertEquals(
			[
				'mysql' => 
				[
					'db'	=> 'users',
					'user'	=> 'u_app',
					'pass'	=> '12345678',
					'host'	=> 'some.ip.address'
				]
			],
			$result
		);
	}
	
	public function test_BaseCase_ConfigObject()
	{
		$subject = new Config();
		$setup = $subject->setup();
		$setup->addStandardLoaders('ini');
		
		$setup->group('dev')
			->addIncludePath(__DIR__ . '/SanityTest/BasicCase/DevConfig');
		
		$setup->group('prod')
			->addIncludePath(__DIR__ . '/SanityTest/BasicCase/ProdConfig');
		
		$object = $subject->getConfigObject();
		$object->merge('main/mysql','main/mysql_2');
		
		$result = $object->get();
		
		self::assertEquals(
			[
				'mysql' => 
				[
					'db'	=> 'users',
					'user'	=> 'u_app',
					'pass'	=> '12345678',
					'host'	=> 'some.ip.address',
					'flags'	=> 1
				]
			],
			$result
		);
	}
}