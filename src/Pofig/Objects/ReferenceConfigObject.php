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
		
		$value = $this->source[$name];
		
		if (is_array($value) && $value && !isset($value[0]))
		{
			$object = new ReferenceConfigObject($this->source[$name]);
			$this->cache[$name] = $object;
			return $object;
		}
		else
		{
			$this->cache[$name] = $value;
			return $value;
		}
	}
	
	public function __set($name, $value)
	{
		throw new PofigException('Set is forbidden on ' . static::class);
	}
	
	public function toArray()
	{
		return $this->source;
	}
	
	
	public function __debugInfo()
	{
		return $this->source;
	}
}