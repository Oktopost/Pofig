<?php
namespace Pofig\Path;


use Pofig\IConfigLoader;
use Pofig\IConfigPath;


class SimplePath implements IConfigPath 
{
	/** @var array */
	private $separators;
	
	/** @var array */
	private $types;
	
	
	public function __construct(array $types, array $separators = ['.', '/', '\\'])
	{
		$this->types		= $types;
		$this->separators	= $separators;
	}
	
	/**
	 * @param string $configName
	 * @return string[]|null
	 */
	public function getFilePath(string $configName): ?array
	{
		$path = str_replace($this->separators, DIRECTORY_SEPARATOR, $configName);
		$result = [];
		
		foreach ($this->types as $type)
		{
			$result[] = $path . '.' . $type;
		}
		
		return $result;
	}
}