<?php
namespace Pofig\Exceptions;


use PHPUnit\Framework\TestCase;


class ConfigFileExceptionTest extends TestCase
{
	public function test_construct_MessageNamesThePath()
	{
		$subject = new ConfigFileException('/a/b.ini');
		self::assertSame('Config file /a/b.ini could not be parsed', $subject->getMessage());
	}
	
	public function test_construct_IsUnexpectedValueExceptionAndNotPofigException()
	{
		$subject = new ConfigFileException('/a/b.ini');
		
		self::assertInstanceOf(\UnexpectedValueException::class, $subject);
		self::assertNotInstanceOf(PofigException::class, $subject);
	}
}
