<?php
namespace Pofig\Objects;


use PHPUnit\Framework\TestCase;


class ReferenceConfigObjectTest extends TestCase
{
	/**
	 * @expectedException \Pofig\Exceptions\PofigException
	 */
	public function test_set_ExceptionThrown()	
	{
		$subject = new ReferenceConfigObject([]);
		$subject->A = 2;
	}
	
	/**
	 * @expectedException \Pofig\Exceptions\PofigException
	 */
	public function test_get_ValueNotPresent_ExceptionThrown()	
	{
		$subject = new ReferenceConfigObject([]);
		$a = $subject->A;
	}
	
	
	public function test_get_IntValue_ValueReturned()	
	{
		$subject = new ReferenceConfigObject(['a' => 1]);
		self::assertEquals(1, $subject->a);
	}
	
	public function test_get_BoolValue_ValueReturned()	
	{
		$subject = new ReferenceConfigObject(['a' => true]);
		self::assertEquals(true, $subject->a);
	}
	
	public function test_get_FloatValue_ValueReturned()	
	{
		$subject = new ReferenceConfigObject(['a' => 0.2]);
		self::assertEquals(0.2, $subject->a);
	}
	
	public function test_get_StringValue_ValueReturned()	
	{
		$subject = new ReferenceConfigObject(['a' => 'b']);
		self::assertEquals('b', $subject->a);
	}
	
	public function test_get_NumericArray_ValueReturned()	
	{
		$subject = new ReferenceConfigObject(['a' => [1, 2]]);
		self::assertEquals([1, 2], $subject->a);
	}
	
	public function test_get_EmptyArray_ValueReturned()	
	{
		$subject = new ReferenceConfigObject(['a' => []]);
		self::assertEquals([], $subject->a);
	}
	
	public function test_get_AssocArray_NewReferenceObjectReturned()	
	{
		$subject = new ReferenceConfigObject(['a' => ['a' => 2]]);
		self::assertInstanceOf(ReferenceConfigObject::class, $subject->a);
	}
	
	public function test_get_AssocArray_ReturnedObjectReferencesTheChildArray()	
	{
		$subject = new ReferenceConfigObject(['a' => ['b' => 'c']]);
		self::assertEquals('c', $subject->a->b);
	}
	
	public function test_get_SameKeyCalledTwice_SameObjectReturned()	
	{
		$subject = new ReferenceConfigObject(['a' => ['b' => 'c']]);
		self::assertSame($subject->a, $subject->a);
	}
	
	public function test_toArray_ConfigReturned()
	{
		$subject = new ReferenceConfigObject(['a' => ['b' => 'c']]);
		self::assertEquals(['a' => ['b' => 'c']], $subject->toArray());
	}
	
	
	public function test_debugInfoReturnsSource()
	{
		$source = ['a' => ['b' => 'c']];
		$subject = new ReferenceConfigObject($source);
		self::assertEquals($source, $subject->__debugInfo());
	}
}