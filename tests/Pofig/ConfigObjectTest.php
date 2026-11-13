<?php
namespace Pofig;


use PHPUnit\Framework\TestCase;
use Pofig\Base\IEngine;
use Pofig\Exceptions\PofigException;
use Pofig\Objects\ReferenceConfigObject;


class ConfigObjectTest extends TestCase
{
	private function subjectWith(array ...$configs): ConfigObject
	{
		$map = [];
		$names = [];
		
		foreach ($configs as $index => $config)
		{
			$map[] = ["config$index", 'array', $config];
			$names[] = "config$index";
		}
		
		$engine = $this->createStub(IEngine::class);
		$engine->method('get')->willReturnMap($map);
		
		$subject = new ConfigObject($engine);
		
		if ($names)
			$subject->merge(...$names);
		
		return $subject;
	}
	
	
	public function test_get_Empty_EmptyArrayReturned()
	{
		self::assertSame([], $this->subjectWith()->get());
	}
	
	public function test_merge_SelfReturned()
	{
		$subject = $this->subjectWith();
		self::assertSame($subject, $subject->merge());
	}
	
	public function test_merge_ConfigsMergedRecursivelyInOrder()
	{
		$subject = $this->subjectWith(
			['a' => ['b' => 1, 'c' => 1], 'd' => 1],
			['a' => ['c' => 2], 'e' => 2]
		);
		
		self::assertSame(['a' => ['b' => 1, 'c' => 2], 'd' => 1, 'e' => 2], $subject->get());
	}
	
	public function test_get_ArrayType_ConfigReturnedWithoutParsing()
	{
		$engine = $this->createMock(IEngine::class);
		$engine->expects($this->never())->method('parse');
		
		$subject = new ConfigObject($engine);
		self::assertSame([], $subject->get('array'));
	}
	
	public function test_get_OtherType_EngineParseResultReturned()
	{
		$engine = $this->createMock(IEngine::class);
		$engine->method('get')->willReturn(['a' => 1]);
		$engine
			->expects($this->once())
			->method('parse')
			->with(['a' => 1], 'SomeType')
			->willReturn('parsed');
		
		$subject = new ConfigObject($engine);
		$subject->merge('n');
		
		self::assertSame('parsed', $subject->get('SomeType'));
	}
	
	public function test_toArray_ConfigReturned()
	{
		self::assertSame(['a' => 1], $this->subjectWith(['a' => 1])->toArray());
	}
	
	public function test_getReferenceObject_ReferenceObjectOfTheConfigReturned()
	{
		$res = $this->subjectWith(['a' => ['b' => 1]])->getReferenceObject();
		
		self::assertInstanceOf(ReferenceConfigObject::class, $res);
		self::assertSame(['a' => ['b' => 1]], $res->toArray());
	}
	
	
	public function test_get_MissingProperty_NullReturned()
	{
		self::assertNull($this->subjectWith()->a);
	}
	
	public function test_get_NullProperty_NullReturned()
	{
		self::assertNull($this->subjectWith(['a' => null])->a);
	}
	
	public function test_get_ScalarProperty_ValueReturned()
	{
		self::assertSame('b', $this->subjectWith(['a' => 'b'])->a);
	}
	
	public function test_get_ListProperty_ListReturned()
	{
		self::assertSame([1, 2], $this->subjectWith(['a' => [1, 2]])->a);
	}
	
	public function test_get_EmptyArrayProperty_EmptyArrayReturned()
	{
		self::assertSame([], $this->subjectWith(['a' => []])->a);
	}
	
	public function test_get_AssocProperty_ReferenceObjectReturned()
	{
		$res = $this->subjectWith(['a' => ['b' => 'c']])->a;
		
		self::assertInstanceOf(ReferenceConfigObject::class, $res);
		self::assertSame('c', $res->b);
	}
	
	public function test_set_ExceptionThrown()
	{
		$this->expectException(PofigException::class);
		
		$subject = $this->subjectWith();
		$subject->a = 1;
	}
	
	public function test_isset_MissingProperty_GotFalse()
	{
		self::assertFalse(isset($this->subjectWith()->a));
	}
	
	public function test_isset_NullProperty_GotFalse()
	{
		self::assertFalse(isset($this->subjectWith(['a' => null])->a));
	}
	
	public function test_isset_PropertyExists_GotTrue()
	{
		self::assertTrue(isset($this->subjectWith(['a' => 1])->a));
	}
}
