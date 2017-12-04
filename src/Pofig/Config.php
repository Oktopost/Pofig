<?php
namespace Pofig;


use Pofig\Base\IConfig;
use Pofig\Base\IMainSetup;
use Pofig\Base\IConfigObject;

use Pofig\Objects\ReferenceConfigObject;
use Structura\Map;


class Config implements IConfig
{
	/** @var Map */
	private $cache;
	
	/** @var Engine */
	private $engine;
	
	/** @var IMainSetup */
	private $setup;
	
	
	private function __sleep() {}
	private function __wakeup() {}
	
	
	public function __construct()
	{
		$this->cache = new Map();
		$this->engine = new Engine();
		$this->setup = $this->engine->setup();
	}
	
	
	public function setup(): IMainSetup
	{
		return $this->setup;
	}
	
	public function get(string $name, string $type = 'array')
	{
		$cacheName = "$name\n$type";
		
		if ($this->cache->tryGet($cacheName, $result))
			return $result;
		
		$result = $this->engine->get($name, $type);
		$this->cache->add($cacheName, $result);
		
		return $result;
	}
	
	public function getReferenceObject(string $name)
	{
		return $this->get($name, ReferenceConfigObject::class);
	}
	
	public function getConfigObject(?string $initialConfigName = null): IConfigObject
	{
		/** @var IConfigObject $config */
		$config = new ConfigObject($this->engine);
		
		if ($initialConfigName)
			$config->merge($initialConfigName);
		
		return $config;
	}
	
	/**
	 * Don't return the cached configuration.
	 * @return array
	 */
	public function __debugInfo()
	{
		return [
			'setup' => $this->setup
		];
	}
	
	public function clearCache(): void
	{
		$this->cache->clear();
	}
}