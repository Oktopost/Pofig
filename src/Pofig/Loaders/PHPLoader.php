<?php
namespace Pofig\Loaders;


use Pofig\IConfigLoader;
use Pofig\Exceptions\ConfigFileException;


class PHPLoader implements IConfigLoader
{
	public function load(string $path): ?array
	{
		/** @noinspection PhpIncludeInspection */
		$config = require $path;
		
		if (!is_array($config))
			throw new ConfigFileException($path);
		
		return $config;
	}
}