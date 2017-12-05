<?php
namespace Pofig\Setup;


use PHPUnit\Framework\TestCase;
use Pofig\Loaders\IniLoader;
use Pofig\Parsers\LiteObjectParser;


class MainSetupTest extends TestCase
{
	public function test_sanity()
	{
		$subject = new MainSetup();
		
		self::assertSame($subject, $subject->addPathResolver());
		self::assertSame($subject, $subject->addStandardLoaders());
		self::assertSame($subject, $subject->addSimplePath(['ini']));
		self::assertSame($subject, $subject->addLoader());
		self::assertSame($subject, $subject->addLoaderForType('a', new IniLoader()));
		self::assertSame($subject, $subject->addParser('a', new LiteObjectParser()));
	}
	
	
	public function test_group_SameObjectReturnedForSameGroup()
	{
		$subject = new MainSetup();
		self::assertSame($subject->group('a'), $subject->group('a'));
	}
	
	public function test_group_DifferentObjectsForDifferentGroups()
	{
		$subject = new MainSetup();
		self::assertNotSame($subject->group('a'), $subject->group('b'));
	}
	
	public function test_group_GroupOrderIsDefinedByFirstCall()
	{
		$subject = new MainSetup();
		
		$a = $subject->group('a');
		$b = $subject->group('b');
		$a = $subject->group('a');
		$c = $subject->group('c');
		$a = $subject->group('a');
		
		self::assertEquals([$a, $b, $c], $subject->getGroups());
	}
	
	
	public function test_groups_NoGroupsCreated_ReturnEmptyArray()
	{
		$subject = new MainSetup();
		self::assertEquals([], $subject->getGroups());
	}
	
	public function test_groups_HasGroups_GroupsReturned()
	{
		$subject = new MainSetup();
		$res = [$subject->group('a'), $subject->group('b')];
		
		self::assertEquals($res, $subject->getGroups());
	}
}