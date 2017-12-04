<?php
namespace Pofig\Path;


use PHPUnit\Framework\TestCase;


class SimplePathTest extends TestCase
{
	public function test_getFilePath_SingleTypePassed_SingleItemReturned()
	{
		$subject = new SimplePath(['a']);
		self::assertEquals(['b.a'], $subject->getFilePath('b'));
	}
	
	public function test_getFilePath_NumberOfTypesPassed_AllTypesReturned()
	{
		$subject = new SimplePath(['a', '1']);
		self::assertEquals(['b.a', 'b.1'], $subject->getFilePath('b'));
	}
	
	
	public function test_getFilePath_SingleSeparatorPresent_SeparatorReplaced()
	{
		$subject = new SimplePath(['a'], ['.']);
		self::assertEquals(['b' . DIRECTORY_SEPARATOR . 'c.a'], $subject->getFilePath('b.c'));
	}
	
	public function test_getFilePath_NumberOfSeparatorsPresent_AllSeparatorsReplaced()
	{
		$subject = new SimplePath(['a'], ['.']);
		self::assertEquals(['b' . DIRECTORY_SEPARATOR . 'c'  . DIRECTORY_SEPARATOR . 'd.a'], $subject->getFilePath('b.c.d'));
	}
	
	public function test_getFilePath_MixedSeparatorsPassed_AllSeparatorsReplaced()
	{
		$subject = new SimplePath(['a'], ['.', '-']);
		self::assertEquals([
			'b' . DIRECTORY_SEPARATOR . 
			'c' . DIRECTORY_SEPARATOR . 
			'd' . DIRECTORY_SEPARATOR . 
			'e.a'], $subject->getFilePath('b.c-d.e'));
	}
}