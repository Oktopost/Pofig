<?php
namespace Pofig\Loaders;


use PHPUnit\Framework\TestCase;


class IniLoaderTest extends TestCase
{
	public function test_sanity_LoadValidFile()
	{
		$subject = new IniLoader();
		$res = $subject->load(__DIR__ . '/IniLoader/valid.ini');
		
		self::assertEquals([
				'test' => [ 'abc' => 123 ],
				'test 2' => 
				[
					'e' => true,
					'f'	=> 'astring'
				]
			],
			$res);
		
	}
}