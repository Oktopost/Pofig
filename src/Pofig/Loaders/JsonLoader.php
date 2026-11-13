<?php
namespace Pofig\Loaders;


use Pofig\IConfigLoader;
use Pofig\Exceptions\ConfigFileException;


class JsonLoader implements IConfigLoader
{
	public function load(string $path): ?array
	{
		$config = json_decode(file_get_contents($path), true);
		
		if (!is_array($config))
			throw new ConfigFileException($path);
		
		return $config;
	}
}