<?php
namespace Pofig\Loaders;


use PHPUnit\Framework\TestCase;


class HierarchicalIniLoaderTest extends TestCase
{
	public function test_sanity_LoadValidFile()
	{
		$subject = new HierarchicalIniLoader();
		$res = $subject->load(__DIR__ . '/HierarchicalIniLoader/valid.ini');
		
		self::assertEquals([
				'a' => 4,
				'b' => 
				[
					'a' => 1,
					'b'	=> 2
				],
				'hello' => 
				[
					'n'	=> 2
				],
				'wor'	=> 
				[
					'ld' => 
					[
						'a' =>
						[
							'a' => 'abc',
							'b'	=> 123
						]
					]
				]
			],
			$res);
	}
}