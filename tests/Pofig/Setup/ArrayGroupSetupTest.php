<?php
namespace Pofig\Setup;


use PHPUnit\Framework\TestCase;
use Pofig\Exceptions\PofigException;


class ArrayGroupSetupTest extends TestCase
{
	public function test_get_EmptyArray_NullReturned()
	{
		self::assertNull((new ArrayGroupSetup([]))->get('a'));
	}
	
	public function test_get_NameMissing_NullReturned()
	{
		self::assertNull((new ArrayGroupSetup(['a' => ['x' => 1]]))->get('b'));
	}
	
	public function test_get_NameExists_ConfigReturned()
	{
		self::assertSame(['x' => 1], (new ArrayGroupSetup(['a' => ['x' => 1]]))->get('a'));
	}
	
	public function test_get_EmptyConfig_EmptyArrayReturned()
	{
		self::assertSame([], (new ArrayGroupSetup(['a' => []]))->get('a'));
	}
	
	public function test_get_KeyWithSlash_FoundByAnySeparator()
	{
		$subject = new ArrayGroupSetup(['redis/queue' => ['x' => 1]]);
		
		self::assertSame(['x' => 1], $subject->get('redis.queue'));
		self::assertSame(['x' => 1], $subject->get('redis/queue'));
		self::assertSame(['x' => 1], $subject->get('redis\\queue'));
	}
	
	public function test_get_KeyWithDot_FoundByAnySeparator()
	{
		$subject = new ArrayGroupSetup(['redis.queue' => ['x' => 1]]);
		
		self::assertSame(['x' => 1], $subject->get('redis.queue'));
		self::assertSame(['x' => 1], $subject->get('redis/queue'));
		self::assertSame(['x' => 1], $subject->get('redis\\queue'));
	}
	
	public function test_get_KeyWithBackslash_FoundByAnySeparator()
	{
		$subject = new ArrayGroupSetup(['redis\\queue' => ['x' => 1]]);
		
		self::assertSame(['x' => 1], $subject->get('redis.queue'));
		self::assertSame(['x' => 1], $subject->get('redis/queue'));
	}
	
	public function test_get_KeyWithMixedSeparators_FoundByAnySeparator()
	{
		$subject = new ArrayGroupSetup(['a.b/c\\d' => ['x' => 1]]);
		
		self::assertSame(['x' => 1], $subject->get('a/b/c/d'));
		self::assertSame(['x' => 1], $subject->get('a.b.c.d'));
	}
	
	public function test_get_CustomSeparators_OnlyCustomSeparatorsReplaced()
	{
		$subject = new ArrayGroupSetup(['a-b' => ['x' => 1]], ['-']);
		
		self::assertSame(['x' => 1], $subject->get('a/b'));
		self::assertSame(['x' => 1], $subject->get('a-b'));
		self::assertNull($subject->get('a.b'));
	}
	
	public function test_get_IntegerKey_FoundByString()
	{
		self::assertSame(['x' => 1], (new ArrayGroupSetup([5 => ['x' => 1]]))->get('5'));
	}
	
	
	public function test_construct_NonArrayValue_ExceptionNamesKeyNotValue()
	{
		try
		{
			new ArrayGroupSetup(['a' => ['x' => 1], 'redis/queue' => 'secret-value']);
			self::fail('Exception expected');
		}
		catch (PofigException $e)
		{
			self::assertStringContainsString("'redis/queue'", $e->getMessage());
			self::assertStringNotContainsString('secret-value', $e->getMessage());
		}
	}
	
	public function test_construct_NullValue_ExceptionThrown()
	{
		$this->expectException(PofigException::class);
		$this->expectExceptionMessage("'a'");
		
		new ArrayGroupSetup(['a' => null]);
	}
	
	public function test_construct_CollidingKeys_ExceptionNamesBothKeys()
	{
		try
		{
			new ArrayGroupSetup(['redis.queue' => ['x' => 'v1'], 'redis/queue' => ['x' => 'v2']]);
			self::fail('Exception expected');
		}
		catch (PofigException $e)
		{
			self::assertStringContainsString("'redis.queue'", $e->getMessage());
			self::assertStringContainsString("'redis/queue'", $e->getMessage());
			self::assertStringNotContainsString('v1', $e->getMessage());
			self::assertStringNotContainsString('v2', $e->getMessage());
		}
	}
	
	
	public function test_debugInfo_OnlyNormalizedNamesReturned()
	{
		$subject = new ArrayGroupSetup(['redis.queue' => ['pass' => 'secret-value'], 'main/mysql' => ['x' => 1]]);
		
		self::assertSame(['configs' => ['redis/queue', 'main/mysql']], $subject->__debugInfo());
	}
	
	public function test_debugInfo_ConfigWithArrayGroupPrinted_ValuesHidden()
	{
		$config = new \Pofig\Config();
		$config->setup()->arrayGroup('prod', ['redis.queue' => ['pass' => 'secret-value']]);
		
		$output = print_r($config, true);
		
		self::assertStringContainsString('redis/queue', $output);
		self::assertStringNotContainsString('secret-value', $output);
		self::assertStringNotContainsString('pass', $output);
	}
}
