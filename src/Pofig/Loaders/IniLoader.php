<?php
namespace Pofig\Loaders;


use Pofig\IConfigLoader;


class IniLoader implements IConfigLoader
{
	private $flags;
	private $loadSections;
	
	
	public function __construct(bool $loadSections = true, int $flags = INI_SCANNER_TYPED)
	{
		$this->loadSections = $loadSections;
		$this->flags = $flags;
	}
	
	public function doLoadSection(?bool $loadSections = null): bool
	{
		if (!is_null($loadSections))
			$this->loadSections = $loadSections;
		
		return $this->loadSections;
	}
	
	public function flags(?int $flags = null): int
	{
		if (!is_null($flags))
			$this->flags = $flags;
		
		return $this->flags;
	}
	
	
	public function load(string $path): ?array
	{
		return parse_ini_file($path, $this->loadSections, $this->flags);
	}
}