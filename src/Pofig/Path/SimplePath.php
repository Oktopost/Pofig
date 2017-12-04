<?php
namespace Pofig\Path;


use Pofig\IConfigLoader;


class SimplePath implements IConfigLoader 
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
	 * @param string $path
	 * @return array|null
	 */
	public function load(string $path): ?array
	{
		$path = str_replace($this->separators, DIRECTORY_SEPARATOR, $path);
		$result = [];
		
		foreach ($this->types as $type)
		{
			$result[] = $path . '.' . $type;
		}
		
		return $result;
	}
}