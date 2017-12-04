<?php
namespace Pofig\Parsers;


use Objection\Mapper;

use Pofig\IConfigParser;


class LiteObjectParser implements IConfigParser
{
	/** @var Mapper */
	private $mapper;
	
	
	public function __construct(?Mapper $mapper = null)
	{
		$this->mapper = $mapper ?: null;
	}


	/**
	 * @param Mapper|null $mapper
	 * @return Mapper|null
	 */
	public function mapper(?Mapper $mapper = null): ?Mapper
	{
		if ($mapper)
			$this->mapper = $mapper;
		
		return $this->mapper;
	}
	
	
	/**
	 * @param array $config
	 * @param string $targetType
	 * @return mixed|null
	 */
	public function parse(array $config, string $targetType)
	{
		if ($this->mapper)
		{
			return $this->mapper->getObject($config, $targetType);
		}
		else
		{
			return Mapper::getObjectFrom($targetType, $config);
		}
	}
}