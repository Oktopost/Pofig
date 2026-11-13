<?php
namespace Pofig;


use PHPUnit\Framework\TestCase;
use Pofig\Loaders\IniLoader;
use Pofig\Setup\MainSetup;
use Pofig\Exceptions\PofigException;


class EngineTest extends TestCase
{
	private ?string $tempDir = null;
	
	
	private function loaderReturning(?array $result): IConfigLoader
	{
		$loader = $this->createStub(IConfigLoader::class);
		$loader->method('load')->willReturn($result);
		return $loader;
	}
	
	private function engineWithIni(string ...$groupDirs): Engine
	{
		$subject = new Engine();
		$subject->setup()->addStandardLoaders('ini');
		
		foreach ($groupDirs as $dir)
		{
			$subject->setup()->group($dir)->addIncludePath(__DIR__ . '/Engine/' . $dir);
		}
		
		return $subject;
	}
	
	
	protected function tearDown(): void
	{
		if ($this->tempDir)
		{
			foreach (glob($this->tempDir . '/*') as $file)
			{
				chmod($file, 0600);
				unlink($file);
			}
			
			rmdir($this->tempDir);
			$this->tempDir = null;
		}
	}
	
	
	public function test_setup_MainSetupReturned()
	{
		$subject = new Engine();
		
		self::assertInstanceOf(MainSetup::class, $subject->setup());
		self::assertSame($subject->setup(), $subject->setup());
	}
	
	
	public function test_parse_ArrayType_ConfigReturned()
	{
		$subject = new Engine();
		self::assertSame(['a' => 1], $subject->parse(['a' => 1], 'array'));
	}
	
	public function test_parse_ParserRegistered_ParserResultReturned()
	{
		$parser = $this->createMock(IConfigParser::class);
		$parser
			->expects($this->once())
			->method('parse')
			->with(['a' => 1], \stdClass::class)
			->willReturn('parsed');
		
		$subject = new Engine();
		$subject->setup()->addParser(\stdClass::class, $parser);
		
		self::assertSame('parsed', $subject->parse(['a' => 1], \stdClass::class));
	}
	
	public function test_parse_NoParserForClass_ExceptionThrown()
	{
		$this->expectException(PofigException::class);
		$this->expectExceptionMessage("No parser found for type 'stdClass'");
		
		(new Engine())->parse([], \stdClass::class);
	}
	
	public function test_parse_TypeIsNotAClass_ExceptionThrown()
	{
		$this->expectException(PofigException::class);
		$this->expectExceptionMessage("No parser found for type 'NotAClass'");
		
		(new Engine())->parse([], 'NotAClass');
	}
	
	
	public function test_get_NoGroups_ExceptionThrown()
	{
		$this->expectException(PofigException::class);
		$this->expectExceptionMessage("No configuration found for 'conf'");
		
		$this->engineWithIni()->get('conf', 'array');
	}
	
	public function test_get_FileNotFound_ExceptionThrown()
	{
		$this->expectException(PofigException::class);
		$this->expectExceptionMessage("No configuration found for 'missing'");
		
		$this->engineWithIni('First')->get('missing', 'array');
	}
	
	public function test_get_SingleGroup_ConfigReturned()
	{
		self::assertSame(['a' => ['x' => 1, 'y' => 1]], $this->engineWithIni('First')->get('conf', 'array'));
	}
	
	public function test_get_GroupsMergedInOrder()
	{
		self::assertSame(['a' => ['x' => 1, 'y' => 2]], $this->engineWithIni('First', 'Second')->get('conf', 'array'));
	}
	
	public function test_get_GroupWithoutTheFile_Skipped()
	{
		self::assertSame(['a' => ['x' => 1, 'y' => 1]], $this->engineWithIni('First', 'Json')->get('conf', 'array'));
	}
	
	public function test_get_TypePassed_ResultParsed()
	{
		$parser = $this->createStub(IConfigParser::class);
		$parser->method('parse')->willReturn('parsed');
		
		$subject = $this->engineWithIni('First');
		$subject->setup()->addParser(\stdClass::class, $parser);
		
		self::assertSame('parsed', $subject->get('conf', \stdClass::class));
	}
	
	public function test_get_GroupLoader_UsedBeforeMainGroupLoader()
	{
		$subject = $this->engineWithIni();
		$subject->setup()->group('g')
			->addIncludePath(__DIR__ . '/Engine/First')
			->addLoaderForType('ini', $this->loaderReturning(['group' => 1]));
		
		self::assertSame(['group' => 1], $subject->get('conf', 'array'));
	}
	
	public function test_get_GroupLoaderReturnsNull_MainGroupLoaderUsed()
	{
		$subject = $this->engineWithIni();
		$subject->setup()->group('g')
			->addIncludePath(__DIR__ . '/Engine/First')
			->addLoaderForType('ini', $this->loaderReturning(null));
		
		self::assertSame(['a' => ['x' => 1, 'y' => 1]], $subject->get('conf', 'array'));
	}
	
	public function test_get_FirstLoaderReturnsNull_NextLoaderUsed()
	{
		$subject = $this->engineWithIni();
		$subject->setup()->group('g')
			->addIncludePath(__DIR__ . '/Engine/First')
			->addLoaderForType('ini', $this->loaderReturning(null))
			->addLoaderForType('ini', $this->loaderReturning(['second' => 1]));
		
		self::assertSame(['second' => 1], $subject->get('conf', 'array'));
	}
	
	public function test_get_NoLoaderForTheFileType_ExceptionThrown()
	{
		$this->expectException(PofigException::class);
		$this->expectExceptionMessage("No configuration found for 'conf'");
		
		$subject = new Engine();
		$subject->setup()->addSimplePath(['json']);
		$subject->setup()->group('g')->addIncludePath(__DIR__ . '/Engine/Json');
		
		$subject->get('conf', 'array');
	}
	
	public function test_get_GroupPathResolver_Used()
	{
		$subject = new Engine();
		$subject->setup()->addStandardLoaders('json');
		$subject->setup()->group('g')
			->addIncludePath(__DIR__ . '/Engine')
			->addSimplePath(['json'], ['-']);
		
		self::assertSame(['j' => 1], $subject->get('Json-conf', 'array'));
	}
	
	public function test_get_AbsoluteFilePath_JoinedWithoutExtraSeparator()
	{
		$resolver = $this->createStub(IConfigPath::class);
		$resolver->method('getFilePath')->willReturn([DIRECTORY_SEPARATOR . 'conf.ini']);
		
		$subject = new Engine();
		$subject->setup()->addLoaderForType('ini', new IniLoader());
		$subject->setup()->addPathResolver($resolver);
		$subject->setup()->group('g')->addIncludePath(__DIR__ . '/Engine/First');
		
		self::assertSame(['a' => ['x' => 1, 'y' => 1]], $subject->get('conf', 'array'));
	}
	
	public function test_get_UnreadableFile_ExceptionThrown()
	{
		$this->tempDir = sys_get_temp_dir() . '/pofig-engine-' . uniqid();
		mkdir($this->tempDir);
		$file = $this->tempDir . '/conf.ini';
		file_put_contents($file, "a = 1\n");
		chmod($file, 0000);
		
		if (is_readable($file))
			self::markTestSkipped('Mode 000 files are readable by this user (root)');
		
		$this->expectException(PofigException::class);
		$this->expectExceptionMessage("File $file is unreadable");
		
		$subject = new Engine();
		$subject->setup()->addStandardLoaders('ini');
		$subject->setup()->group('g')->addIncludePath($this->tempDir);
		
		$subject->get('conf', 'array');
	}
}
