<?php
namespace Pofig\Objects;


use Pofig\Exceptions\PofigException;
use Pofig\Exceptions\MissingConfigPropertyException;


class ReferenceConfigObject
{
	private $source;
	
	/** @var ReferenceConfigObject[] */
	private $cache = [];
	
	
	public function __construct(array $source)
	{
		$this->source = $source;
	}
	
	
	public function __get($name)
	{
		if (isset($this->cache[$name]))
			return $this->cache[$name];
		
		if (!isset($this->source[$name]))
			throw new MissingConfigPropertyException($name);
		
		if (is_array($this->source[$name]) && !isset($this->source[$name][0]))
		{
			$object = new ReferenceConfigObject($this->source[$name]);
			$this->cache[$name] = $object;
			return $object;
		}
		else
		{
			$object = $this->source[$name];
			$this->cache[$name] = $object;
			return $object;
		}
	}
	
	public function __set($name, $value)
	{
		throw new PofigException('Set is forbidden on ' . static::class);
	}
	
	
	public function __debugInfo()
	{
		return $this->source;
	}
}