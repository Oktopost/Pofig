<?php
namespace Pofig\Loaders;


use PHPUnit\Framework\TestCase;
use Pofig\Exceptions\PofigException;
use Pofig\Exceptions\ConfigFileException;


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
	
	
	public function test_doLoadSection_DefaultIsTrue()
	{
		$subject = new HierarchicalIniLoader();
		self::assertTrue($subject->doLoadSection());
	}
	
	public function test_doLoadSection_ValuePassed_ValueSetAndReturned()
	{
		$subject = new HierarchicalIniLoader();
		
		self::assertFalse($subject->doLoadSection(false));
		self::assertFalse($subject->doLoadSection());
	}
	
	public function test_flags_DefaultIsTyped()
	{
		$subject = new HierarchicalIniLoader();
		self::assertSame(INI_SCANNER_TYPED, $subject->flags());
	}
	
	public function test_flags_ValuePassed_ValueSetAndReturned()
	{
		$subject = new HierarchicalIniLoader();
		
		self::assertSame(INI_SCANNER_RAW, $subject->flags(INI_SCANNER_RAW));
		self::assertSame(INI_SCANNER_RAW, $subject->flags());
	}
	
	public function test_keyDelimiter_DefaultIsDot()
	{
		$subject = new HierarchicalIniLoader();
		self::assertSame('.', $subject->keyDelimiter());
	}
	
	public function test_keyDelimiter_ValuePassed_ValueSetAndReturned()
	{
		$subject = new HierarchicalIniLoader();
		
		self::assertSame('_', $subject->keyDelimiter('_'));
		self::assertSame('_', $subject->keyDelimiter());
	}
	
	public function test_load_ConstructorArgumentsUsed()
	{
		$subject = new HierarchicalIniLoader(false, INI_SCANNER_RAW);
		
		self::assertSame(
			['a' => '1', 'b' => ['c' => '2']], 
			$subject->load(__DIR__ . '/HierarchicalIniLoader/sections.ini'));
	}
	
	public function test_load_SettersUsed()
	{
		$subject = new HierarchicalIniLoader();
		$subject->doLoadSection(false);
		$subject->flags(INI_SCANNER_RAW);
		
		self::assertSame(
			['a' => '1', 'b' => ['c' => '2']], 
			$subject->load(__DIR__ . '/HierarchicalIniLoader/sections.ini'));
	}
	
	public function test_load_CustomKeyDelimiter_KeysSplitOnTheDelimiter()
	{
		$subject = new HierarchicalIniLoader();
		$subject->keyDelimiter('_');
		
		self::assertSame(
			['a' => ['b' => 1], 'c.d' => 2], 
			$subject->load(__DIR__ . '/HierarchicalIniLoader/custom-delimiter.ini'));
	}
	
	public function test_load_DuplicateKey_ExceptionThrown()
	{
		$this->expectException(PofigException::class);
		$this->expectExceptionMessage('The key a is already set');
		
		$subject = new HierarchicalIniLoader();
		$subject->load(__DIR__ . '/HierarchicalIniLoader/duplicate.ini');
	}
	
	public function test_load_KeyIsBothValueAndChild_ExceptionThrown()
	{
		$this->expectException(PofigException::class);
		$this->expectExceptionMessage('The key a can not be set both as value and a child array');
		
		$subject = new HierarchicalIniLoader();
		$subject->load(__DIR__ . '/HierarchicalIniLoader/value-and-child.ini');
	}
	
	public function test_load_EmptyFile_EmptyArrayReturned()
	{
		$subject = new HierarchicalIniLoader();
		self::assertSame([], $subject->load(__DIR__ . '/HierarchicalIniLoader/empty.ini'));
	}
	
	public function test_load_InvalidFile_ConfigFileExceptionThrown()
	{
		$subject = new HierarchicalIniLoader();
		$path = __DIR__ . '/HierarchicalIniLoader/invalid.ini';
		
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
