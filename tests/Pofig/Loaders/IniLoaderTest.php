<?php
namespace Pofig\Loaders;


use PHPUnit\Framework\TestCase;
use Pofig\Exceptions\PofigException;
use Pofig\Exceptions\ConfigFileException;


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
	
	
	public function test_doLoadSection_DefaultIsTrue()
	{
		$subject = new IniLoader();
		self::assertTrue($subject->doLoadSection());
	}
	
	public function test_doLoadSection_ValuePassed_ValueSetAndReturned()
	{
		$subject = new IniLoader();
		
		self::assertFalse($subject->doLoadSection(false));
		self::assertFalse($subject->doLoadSection());
	}
	
	public function test_flags_DefaultIsTyped()
	{
		$subject = new IniLoader();
		self::assertSame(INI_SCANNER_TYPED, $subject->flags());
	}
	
	public function test_flags_ValuePassed_ValueSetAndReturned()
	{
		$subject = new IniLoader();
		
		self::assertSame(INI_SCANNER_RAW, $subject->flags(INI_SCANNER_RAW));
		self::assertSame(INI_SCANNER_RAW, $subject->flags());
	}
	
	public function test_load_ConstructorArgumentsUsed()
	{
		$subject = new IniLoader(false, INI_SCANNER_RAW);
		
		self::assertSame(['a' => 'true', 'b' => '2'], $subject->load(__DIR__ . '/IniLoader/sections.ini'));
	}
	
	public function test_load_SettersUsed()
	{
		$subject = new IniLoader();
		$subject->doLoadSection(false);
		$subject->flags(INI_SCANNER_RAW);
		
		self::assertSame(['a' => 'true', 'b' => '2'], $subject->load(__DIR__ . '/IniLoader/sections.ini'));
	}
	
	public function test_load_EmptyFile_EmptyArrayReturned()
	{
		$subject = new IniLoader();
		self::assertSame([], $subject->load(__DIR__ . '/IniLoader/empty.ini'));
	}
	
	public function test_load_InvalidFile_ConfigFileExceptionThrown()
	{
		$subject = new IniLoader();
		$path = __DIR__ . '/IniLoader/invalid.ini';
		
		try
		{
			$subject->load($path);
			self::fail('Expected ' . ConfigFileException::class);
		}
		catch (ConfigFileException $e)
		{
			self::assertNotInstanceOf(PofigException::class, $e);
			self::assertStringContainsString($path, $e->getMessage());
			self::assertStringNotContainsString('s3cret', $e->getMessage());
		}
	}
}
