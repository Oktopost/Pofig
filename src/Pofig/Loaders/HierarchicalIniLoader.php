<?php
namespace Pofig\Loaders;


use Pofig\Exceptions\PofigException;
use Pofig\IConfigLoader;


class HierarchicalIniLoader implements IConfigLoader
{
	private $flags;
	private $loadSections;
	private $keyDelimiter = '.';
	
	
	private function setChildValue(string $key, $value, &$target)
	{
		$dot = strpos($key, $this->keyDelimiter);
		
		if ($dot === false)
		{
			if (isset($target[$key]))
				throw new PofigException('The key ' . $key . ' is already set');
			
			if (is_array($value))
			{
				$target[$key] = [];
				
				foreach ($value as $k => $val)
				{
					$this->setChildValue($k, $val, $target[$key]);
				}
			}
			else
			{
				$target[$key] = $value;
			}
		}
		else
		{
			list ($key, $reminder) = explode('.', $key, 2);
			
			if (isset($target[$key]))
			{
				if (!is_array($target[$key]))
					throw new PofigException('The key ' . $key . ' can not be set both as value and a child array');
			}
			else
			{
				$target[$key] = [];
			}
			
			$this->setChildValue($reminder, $value, $target[$key]);
		}
	}
	
	
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
	
	public function keyDelimiter(?string $delimiter = null): string
	{
		if (!is_null($delimiter))
			$this->keyDelimiter = $delimiter;
		
		return $this->keyDelimiter;
	}
	
	
	public function load(string $path): ?array
	{
		$config = parse_ini_file($path, $this->loadSections, $this->flags);
		$result = [];
		
		foreach ($config as $key => $value)
		{
			$this->setChildValue($key, $value, $result);
		}
		
		return $result;
	}
}