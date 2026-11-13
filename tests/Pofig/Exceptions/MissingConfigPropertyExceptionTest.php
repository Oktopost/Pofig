<?php
namespace Pofig\Exceptions;


use PHPUnit\Framework\TestCase;


class MissingConfigPropertyExceptionTest extends TestCase
{
	public function test_getMissingPropertyName_NameReturned()
	{
		$subject = new MissingConfigPropertyException('abc');
		self::assertSame('abc', $subject->getMissingPropertyName());
	}
	
	public function test_construct_MessageAndCodeSet()
	{
		$subject = new MissingConfigPropertyException('abc');
		
		self::assertSame('Property abc not found in the configuration data set', $subject->getMessage());
		self::assertSame(1, $subject->getCode());
	}
	
	public function test_construct_IsPofigException()
	{
		self::assertInstanceOf(PofigException::class, new MissingConfigPropertyException('abc'));
	}
}
