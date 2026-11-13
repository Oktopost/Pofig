<?php
namespace Pofig\Setup;


use PHPUnit\Framework\TestCase;
use Pofig\IConfigPath;
use Pofig\IConfigLoader;
use Pofig\IConfigParser;
use Pofig\Loaders\IniLoader;
use Pofig\Loaders\JsonLoader;
use Pofig\Loaders\PHPLoader;
use Pofig\Parsers\LiteObjectParser;
use Pofig\Exceptions\PofigException;


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
	
	
	public function test_addPathResolver_AddedToMainGroup()
	{
		$resolver = $this->createStub(IConfigPath::class);
		$resolver->method('getFilePath')->willReturn(['a']);
		
		$subject = new MainSetup();
		$subject->addPathResolver($resolver);
		
		self::assertSame(['a'], $subject->getMainGroup()->getPathFor('x'));
	}
	
	public function test_addSimplePath_AddedToMainGroup()
	{
		$subject = new MainSetup();
		$subject->addSimplePath(['ini'], ['-']);
		
		self::assertSame(['a' . DIRECTORY_SEPARATOR . 'b.ini'], $subject->getMainGroup()->getPathFor('a-b'));
	}
	
	public function test_addLoader_AddedToMainGroup()
	{
		$loader = $this->createStub(IConfigLoader::class);
		
		$subject = new MainSetup();
		$subject->addLoader($loader);
		
		self::assertSame([$loader], $subject->getMainGroup()->getLoadersFor('ini'));
	}
	
	public function test_addLoaderForType_AddedToMainGroup()
	{
		$loader = $this->createStub(IConfigLoader::class);
		
		$subject = new MainSetup();
		$subject->addLoaderForType('ini', $loader);
		
		self::assertSame([$loader], $subject->getMainGroup()->getLoadersFor('ini'));
	}
	
	public function test_getMainGroup_NotPartOfGroups()
	{
		$subject = new MainSetup();
		
		self::assertInstanceOf(GroupSetup::class, $subject->getMainGroup());
		self::assertSame($subject->getMainGroup(), $subject->getMainGroup());
		self::assertSame([], $subject->getGroups());
	}
	
	
	public function test_addStandardLoaders_LoadersAndPathsAddedToMainGroup()
	{
		$subject = new MainSetup();
		$subject->addStandardLoaders('ini', 'php', 'json');
		$group = $subject->getMainGroup();
		
		self::assertInstanceOf(IniLoader::class, $group->getLoadersFor('ini')[0]);
		self::assertInstanceOf(PHPLoader::class, $group->getLoadersFor('php')[0]);
		self::assertInstanceOf(JsonLoader::class, $group->getLoadersFor('json')[0]);
		self::assertSame(['a.ini', 'a.php', 'a.json'], $group->getPathFor('a'));
	}
	
	public function test_addStandardLoaders_NonStandardType_ExceptionThrown()
	{
		$this->expectException(PofigException::class);
		$this->expectExceptionMessage("The type 'xml' is not standard");
		
		(new MainSetup())->addStandardLoaders('xml');
	}
	
	
	public function test_getParserFor_ExactType_ParserReturned()
	{
		$parser = $this->createStub(IConfigParser::class);
		
		$subject = new MainSetup();
		$subject->addParser('a', $parser);
		
		self::assertSame($parser, $subject->getParserFor('a'));
	}
	
	public function test_getParserFor_NoParsers_NullReturned()
	{
		self::assertNull((new MainSetup())->getParserFor(MainSetupTest_Child::class));
	}
	
	public function test_getParserFor_TypeIsNotAClassOrInterface_NullReturned()
	{
		$subject = new MainSetup();
		$subject->addParser(MainSetupTest_Interface::class, $this->createStub(IConfigParser::class));
		
		self::assertNull($subject->getParserFor('NotAClass'));
	}
	
	public function test_getParserFor_ParserForParentClass_ParserReturned()
	{
		$parser = $this->createStub(IConfigParser::class);
		
		$subject = new MainSetup();
		$subject->addParser(MainSetupTest_Parent::class, $parser);
		
		self::assertSame($parser, $subject->getParserFor(MainSetupTest_Child::class));
	}
	
	public function test_getParserFor_ParserForInterface_ParserReturned()
	{
		$parser = $this->createStub(IConfigParser::class);
		
		$subject = new MainSetup();
		$subject->addParser(MainSetupTest_Interface::class, $parser);
		
		self::assertSame($parser, $subject->getParserFor(MainSetupTest_Child::class));
	}
	
	public function test_getParserFor_ParserForUnrelatedInterface_NullReturned()
	{
		$subject = new MainSetup();
		$subject->addParser(MainSetupTest_Interface::class, $this->createStub(IConfigParser::class));
		
		self::assertNull($subject->getParserFor(MainSetupTest_Parent::class));
	}
	
	public function test_getParserFor_ParserForUnrelatedClass_NullReturned()
	{
		$subject = new MainSetup();
		$subject->addParser(MainSetupTest_Parent::class, $this->createStub(IConfigParser::class));
		
		self::assertNull($subject->getParserFor(MainSetupTest_Other::class));
	}
	
	public function test_getParserFor_ParserKeyIsNotAClass_NullReturned()
	{
		$subject = new MainSetup();
		$subject->addParser('a', $this->createStub(IConfigParser::class));
		
		self::assertNull($subject->getParserFor(MainSetupTest_Child::class));
	}
	
	public function test_getParserFor_UnrelatedParsersBeforeMatch_MatchReturned()
	{
		$parser = $this->createStub(IConfigParser::class);
		
		$subject = new MainSetup();
		$subject->addParser('a', $this->createStub(IConfigParser::class));
		$subject->addParser(MainSetupTest_Other::class, $this->createStub(IConfigParser::class));
		$subject->addParser(MainSetupTest_Interface::class, $parser);
		
		self::assertSame($parser, $subject->getParserFor(MainSetupTest_Child::class));
	}
	
	public function test_getParserFor_MatchFound_MatchKeptForTheType()
	{
		$parser = $this->createStub(IConfigParser::class);
		$other = $this->createStub(IConfigParser::class);
		
		$subject = new MainSetup();
		$subject->addParser(MainSetupTest_Parent::class, $parser);
		$subject->getParserFor(MainSetupTest_Child::class);
		$subject->addParser(MainSetupTest_Parent::class, $other);
		
		self::assertSame($parser, $subject->getParserFor(MainSetupTest_Child::class));
	}
}


interface MainSetupTest_Interface {}
class MainSetupTest_Parent {}
class MainSetupTest_Child extends MainSetupTest_Parent implements MainSetupTest_Interface {}
class MainSetupTest_Other {}
