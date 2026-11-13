<?php
namespace Pofig;


use Pofig\Base\IConfig;
use Pofig\Base\IMainSetup;
use Pofig\Base\IConfigObject;
use Pofig\Objects\ReferenceConfigObject;
use Pofig\Exceptions\PofigException;

use Structura\Map;


class Config implements IConfig
{
	/** @var Map */
	private $cache;
	
	/** @var Engine */
	private $engine;
	
	/** @var IMainSetup */
	private $setup;
	
	
	public function __serialize(): array
	{
		throw new PofigException('Config can not be serialized');
	}
	
	public function __unserialize(array $data): void
	{
		throw new PofigException('Config can not be unserialized');
	}
	
	
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
	
	public function clearCache(): void
	{
		$this->cache->clear();
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
}