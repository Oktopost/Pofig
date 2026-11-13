<?php
namespace Pofig\Loaders;


use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Pofig\Exceptions\PofigException;
use Pofig\Exceptions\ConfigFileException;


class JsonLoaderTest extends TestCase
{
	public static function provideUnparsableFiles(): array
	{
		return [
			'invalid json'	=> ['invalid.json'],
			'scalar json'	=> ['scalar.json']
		];
	}
	
	
	public function test_load_ValidFile_ArrayReturned()
	{
		$subject = new JsonLoader();
		
		self::assertSame(
			['a' => 1, 'b' => ['c' => 'd'], 'e' => [1, 2]], 
			$subject->load(__DIR__ . '/JsonLoader/valid.json'));
	}
	
	#[DataProvider('provideUnparsableFiles')]
	public function test_load_UnparsableFile_ConfigFileExceptionThrown(string $file)
	{
		$subject = new JsonLoader();
		$path = __DIR__ . '/JsonLoader/' . $file;
		
		try
		{
			$subject->load($path);
			self::fail('Expected ' . ConfigFileException::class);
		}
		catch (ConfigFileException $e)
		{
			self::assertNotInstanceOf(PofigException::class, $e);
			self::assertStringContainsString($path, $e->getMessage());
			self::assertStringNotContainsString('s3cret', $e->getMessage());
		}
	}
}
