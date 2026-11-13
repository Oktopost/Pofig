<?php
namespace Pofig;


use PHPUnit\Framework\TestCase;
use Pofig\Exceptions\PofigException;


class ArrayGroupTest extends TestCase
{
	private const FIRST	= ['conn' => ['host' => 'first', 'port' => 1, 'user' => 'first']];
	private const LAST	= ['conn' => ['user' => 'last']];
	private const MEMORY	= ['conn' => ['host' => 'memory', 'pass' => 'memory'], 'extra' => ['a' => 1]];
	
	
	private function config(): Config
	{
		$config = new Config();
		$config->setup()->addStandardLoaders('ini');
		return $config;
	}
	
	private function addFileGroup(Config $config, string $name, string $dir): void
	{
		$config->setup()->group($name)->addIncludePath(__DIR__ . '/ArrayGroup/' . $dir);
	}
	
	
	public function test_get_FileThenMemoryThenFile_MergedInRegistrationOrder()
	{
		$config = $this->config();
		$this->addFileGroup($config, 'first', 'First');
		$config->setup()->arrayGroup('memory', ['main/mysql' => self::MEMORY]);
		$this->addFileGroup($config, 'last', 'Last');
		
		self::assertSame(
			array_replace_recursive(self::FIRST, self::MEMORY, self::LAST),
			$config->get('main/mysql'));
	}
	
	public function test_get_MemoryFirst_MergedFirst()
	{
		$config = $this->config();
		$config->setup()->arrayGroup('memory', ['main/mysql' => self::MEMORY]);
		$this->addFileGroup($config, 'first', 'First');
		$this->addFileGroup($config, 'last', 'Last');
		
		self::assertSame(
			array_replace_recursive(self::MEMORY, self::FIRST, self::LAST),
			$config->get('main/mysql'));
	}
	
	public function test_get_MemoryLast_MergedLast()
	{
		$config = $this->config();
		$this->addFileGroup($config, 'first', 'First');
		$this->addFileGroup($config, 'last', 'Last');
		$config->setup()->arrayGroup('memory', ['main/mysql' => self::MEMORY]);
		
		self::assertSame(
			array_replace_recursive(self::FIRST, self::LAST, self::MEMORY),
			$config->get('main/mysql'));
	}
	
	public function test_get_OnlyMemoryGroup_ConfigReturned()
	{
		$config = $this->config();
		$config->setup()->arrayGroup('memory', ['main/mysql' => self::MEMORY]);
		
		self::assertSame(self::MEMORY, $config->get('main/mysql'));
	}
	
	public function test_get_AnySeparator_SameConfigReturned()
	{
		$config = $this->config();
		$config->setup()->arrayGroup('memory', ['redis/queue' => ['a' => 1], 'main.mysql' => ['b' => 2]]);
		
		self::assertSame(['a' => 1], $config->get('redis.queue'));
		self::assertSame(['a' => 1], $config->get('redis/queue'));
		self::assertSame(['a' => 1], $config->get('redis\\queue'));
		self::assertSame(['b' => 2], $config->get('main/mysql'));
	}
	
	public function test_get_DotSeparatedName_MergedWithFileGroups()
	{
		$config = $this->config();
		$this->addFileGroup($config, 'first', 'First');
		$config->setup()->arrayGroup('memory', ['main.mysql' => self::MEMORY]);
		
		self::assertSame(
			array_replace_recursive(self::FIRST, self::MEMORY),
			$config->get('main.mysql'));
	}
	
	public function test_get_NameMissingInMemory_FileConfigReturnedUnchanged()
	{
		$config = $this->config();
		$this->addFileGroup($config, 'first', 'First');
		$config->setup()->arrayGroup('memory', ['main/mysql' => self::MEMORY]);
		
		self::assertSame(['a' => ['b' => 1]], $config->get('main/files-only'));
	}
	
	public function test_get_NameMissingEverywhere_ExceptionThrown()
	{
		$this->expectException(PofigException::class);
		$this->expectExceptionMessage("No configuration found for 'main/missing'");
		
		$config = $this->config();
		$this->addFileGroup($config, 'first', 'First');
		$config->setup()->arrayGroup('memory', ['main/mysql' => self::MEMORY]);
		
		$config->get('main/missing');
	}
	
	public function test_get_EmptyMemoryGroupOnly_ExceptionThrown()
	{
		$this->expectException(PofigException::class);
		$this->expectExceptionMessage("No configuration found for 'a'");
		
		$config = $this->config();
		$config->setup()->arrayGroup('memory', []);
		
		$config->get('a');
	}
}
