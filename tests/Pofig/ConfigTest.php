<?php
namespace Pofig;


use PHPUnit\Framework\TestCase;
use Pofig\Exceptions\PofigException;
use Pofig\Exceptions\ConfigFileException;
use Pofig\Objects\ReferenceConfigObject;
use Pofig\Parsers\ConfigObjectParser;
use Pofig\Setup\MainSetup;


class ConfigTest extends TestCase
{
	private function subject(?IConfigLoader $loader = null): Config
	{
		$subject = new Config();
		$setup = $subject->setup();
		
		$setup->addSimplePath(['ini']);
		$setup->addLoaderForType('ini', $loader ?? new Loaders\IniLoader());
		$setup->group('main')->addIncludePath(__DIR__ . '/Config/Files');
		
		return $subject;
	}
	
	private function countingLoader(int $times): IConfigLoader
	{
		$loader = $this->createMock(IConfigLoader::class);
		$loader
			->expects($this->exactly($times))
			->method('load')
			->willReturn(['a' => 1]);
		
		return $loader;
	}
	
	
	public function test_setup_MainSetupReturned()
	{
		$subject = new Config();
		
		self::assertInstanceOf(MainSetup::class, $subject->setup());
		self::assertSame($subject->setup(), $subject->setup());
	}
	
	public function test_get_ConfigReturned()
	{
		self::assertSame(['a' => ['b' => 1]], $this->subject()->get('conf'));
	}
	
	public function test_get_CalledTwice_LoadedOnce()
	{
		$subject = $this->subject($this->countingLoader(1));
		
		self::assertSame(['a' => 1], $subject->get('conf'));
		self::assertSame(['a' => 1], $subject->get('conf'));
	}
	
	public function test_clearCache_ConfigLoadedAgain()
	{
		$subject = $this->subject($this->countingLoader(2));
		
		$subject->get('conf');
		$subject->clearCache();
		$subject->get('conf');
	}
	
	public function test_getReferenceObject_ReferenceObjectReturned()
	{
		$subject = $this->subject();
		$subject->setup()->addParser(ReferenceConfigObject::class, new ConfigObjectParser());
		
		$res = $subject->getReferenceObject('conf');
		
		self::assertInstanceOf(ReferenceConfigObject::class, $res);
		self::assertSame(1, $res->a->b);
	}
	
	public function test_getConfigObject_NoName_EmptyObjectReturned()
	{
		self::assertSame([], $this->subject()->getConfigObject()->toArray());
	}
	
	public function test_getConfigObject_WithName_ConfigMerged()
	{
		$res = $this->subject()->getConfigObject('conf');
		self::assertSame(['a' => ['b' => 1]], $res->toArray());
	}
	
	public function test_getConfigObject_NewObjectEachCall()
	{
		$subject = $this->subject();
		self::assertNotSame($subject->getConfigObject(), $subject->getConfigObject());
	}
	
	public function test_get_GroupGenericJsonLoader_ThrowsInsteadOfFallingThroughToMainIniLoader()
	{
		$this->expectException(ConfigFileException::class);
		
		$subject = $this->subject();
		$subject->setup()->group('main')->addLoader([new Loaders\JsonLoader()]);
		
		$subject->get('conf');
	}
	
	public function test_serialize_ExceptionThrown()
	{
		$this->expectException(PofigException::class);
		serialize(new Config());
	}
	
	public function test_unserialize_ExceptionThrown()
	{
		$this->expectException(PofigException::class);
		unserialize('O:12:"Pofig\Config":0:{}');
	}
	
	public function test_debugInfo_OnlySetupReturned()
	{
		$subject = $this->subject();
		$subject->get('conf');
		
		self::assertSame(['setup' => $subject->setup()], $subject->__debugInfo());
	}
}
