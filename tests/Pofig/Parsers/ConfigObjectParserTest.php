<?php
namespace Pofig\Parsers;


use PHPUnit\Framework\TestCase;
use Pofig\Objects\ReferenceConfigObject;


class ConfigObjectParserTest extends TestCase
{
	public function test_ReferenceObjectReturned()
	{
		$subject = new ConfigObjectParser();
		self::assertInstanceOf(ReferenceConfigObject::class, $subject->parse([], ReferenceConfigObject::class));
	}
	
	public function test_ReferenceObjectHaveThePassedData()
	{
		$subject = new ConfigObjectParser();
		self::assertEquals('a', $subject->parse(['b' => 'a'], ReferenceConfigObject::class)->b);
	}
}