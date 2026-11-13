<?php
namespace Pofig\Loaders;


use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Pofig\Exceptions\PofigException;
use Pofig\Exceptions\ConfigFileException;


class PHPLoaderTest extends TestCase
{
	public static function provideNonArrayFiles(): array
	{
		return [
			'returns a scalar'	=> ['scalar.php'],
			'returns nothing'	=> ['no-return.php']
		];
	}
	
	
	public function test_load_ValidFile_ArrayReturned()
	{
		$subject = new PHPLoader();
		
		self::assertSame(
			['a' => 1, 'b' => ['c' => 'd']], 
			$subject->load(__DIR__ . '/PHPLoader/valid.php'));
	}
	
	#[DataProvider('provideNonArrayFiles')]
	public function test_load_NonArrayResult_ConfigFileExceptionThrown(string $file)
	{
		$subject = new PHPLoader();
		$path = __DIR__ . '/PHPLoader/' . $file;
		
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
