<?php
namespace Pofig\Loaders;


use Pofig\IConfigLoader;


class JsonLoader implements IConfigLoader
{
	public function load(string $path): ?array
	{
		return json_decode(file_get_contents($path), true);
	}
}