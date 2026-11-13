<?php
namespace Pofig;


use Pofig\Base\IEngine;
use Pofig\Base\IConfigObject;
use Pofig\Objects\ReferenceConfigObject;
use Pofig\Exceptions\PofigException;


class ConfigObject implements IConfigObject
{
	/** @var IEngine */
	private $engine;
	
	/** @var array */
	private $config = [];
	
	
	public function __construct(IEngine $engine)
	{
		$this->engine = $engine;
	}
	
	
	/**
	 * @param string $name
	 * @return mixed|ReferenceConfigObject
	 */
	public function __get($name)
	{
		if (!isset($this->config[$name]))
			return null;
		
		$value = $this->config[$name];
		
		if (is_array($value) && $value && !isset($value[0]))
		{
			return new ReferenceConfigObject($value);
		}
		else
		{
			return $value;
		}
	}
	
	/**
	 * Troughs exception
	 * @param $name
	 * @param $value
	 */
	public function __set($name, $value)
	{
		throw new PofigException('Set operation is forbidden on ConfigObject');
	}
	
	public function __isset($name)
	{
		return isset($this->config[$name]);
	}
	
	/**
	 * @param string[] $with
	 * @return IConfigObject|static
	 */
	public function merge(string ...$with): IConfigObject
	{
		foreach ($with as $configName)
		{
			$res = $this->engine->get($configName, 'array');
			$this->config = array_replace_recursive($this->config, $res);
		}
		
		return $this;
	}
	
	/**
	 * @param string $type
	 * @return mixed
	 */
	public function get(string $type = 'array')
	{
		if ($type == 'array')
			return $this->config;
		
		return $this->engine->parse($this->config, $type);
	}
	
	/**
	 * @return ReferenceConfigObject
	 */
	public function getReferenceObject(): ReferenceConfigObject
	{
		return new ReferenceConfigObject($this->config);
	}
	
	public function toArray(): array
	{
		return $this->config;
	}
}