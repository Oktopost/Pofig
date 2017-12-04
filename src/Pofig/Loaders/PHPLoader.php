<?php
namespace Pofig\Loaders;


use Pofig\IConfigLoader;


class PHPLoader implements IConfigLoader
{
	public function load(string $path): ?array
	{
		/** @noinspection PhpIncludeInspection */
		return require $path;
	}
}