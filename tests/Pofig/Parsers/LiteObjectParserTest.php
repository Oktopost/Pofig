<?php
namespace Pofig\Parsers;


use Objection\LiteSetup;
use Objection\LiteObject;
use Objection\Mapper;
use PHPUnit\Framework\TestCase;


class LiteObjectParserTest extends TestCase
{
	public function test_mapper_NoMapper_NullReturned()
	{
		$subject = new LiteObjectParser();
		self::assertNull($subject->mapper());
	}
	
	public function test_mapper_MapperSet_MapperReturned()
	{
		$mapper = Mapper::create();
		$subject = new LiteObjectParser($mapper);
		self::assertSame($mapper, $subject->mapper());
	}
	
	public function test_mapper_MapperPassed_NewMapperReturned()
	{
		$mapper = Mapper::create();
		$subject = new LiteObjectParser();
		self::assertSame($mapper, $subject->mapper($mapper));
	}
	
	public function test_mapper_MapperPassed_MapperSetAsNewMapper()
	{
		$mapper = Mapper::create();
		$subject = new LiteObjectParser();
		
		$subject->mapper($mapper);
		self::assertSame($mapper, $subject->mapper());
	}
	
	
	public function test_parse_MapperSet_MapperInvoked()
	{
		$mapper = $this->getMockBuilder(Mapper::class)->disableOriginalConstructor()->getMock();
		$source = ['a' => 'b'];
		
		$mapper
			->expects($this->once())
			->method('getObject')
			->with($source, LiteObjectParserTest_Helper::class);
		
		$subject = new LiteObjectParser($mapper);
		$subject->parse($source, LiteObjectParserTest_Helper::class);
	}
	
	public function test_parse_MapperSet_MapperResultInvoked()
	{
		$mapper = $this->getMockBuilder(Mapper::class)->disableOriginalConstructor()->getMock();
		
		$mapper
			->method('getObject')
			->willReturn('abc');
		
		$subject = new LiteObjectParser($mapper);
		self::assertEquals('abc', $subject->parse([], LiteObjectParserTest_Helper::class));
	}
	
	public function test_parse_MapperNotSet_InstanceReturned()
	{
		$subject = new LiteObjectParser();
		self::assertInstanceOf(LiteObjectParserTest_Helper::class, $subject->parse([], LiteObjectParserTest_Helper::class));
	}
	
	public function test_parse_MapperNotSet_ObjectParsed()
	{
		$subject = new LiteObjectParser();
		
		$res = $subject->parse(['A' => 'nnn'], LiteObjectParserTest_Helper::class);
		self::assertEquals('nnn', $res->A);
	}
}


class LiteObjectParserTest_Helper extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'A'	=> LiteSetup::createString('b')
		];
	}
}