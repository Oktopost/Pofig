<?php
namespace Pofig\Setup;


use Pofig\Exceptions\PofigException;


class ArrayGroupSetup
{
	private const NORMALIZED_SEPARATOR = '/';
	
	
	/** @var array */
	private $separators;
	
	/** @var array[] Config name, normalized to '/', => config */
	private $configs = [];
	
	
	private function normalize(string $configName): string
	{
		return str_replace($this->separators, self::NORMALIZED_SEPARATOR, $configName);
	}
	
	
	/**
	 * @param array $configs Config name => config array
	 * @param array $separators
	 */
	public function __construct(array $configs, array $separators = ['.', '/', '\\'])
	{
		$this->separators = $separators;
		
		/** @var string[] $originalKeys Normalized name => key as given */
		$originalKeys = [];
		
		foreach ($configs as $key => $config)
		{
			$key = (string)$key;
			
			if (!is_array($config))
				throw new PofigException("The config '$key' in the array group must be an array");
			
			$name = $this->normalize($key);
			
			if (isset($originalKeys[$name]))
				throw new PofigException(
					"The configs '{$originalKeys[$name]}' and '$key' in the array group resolve to the same name");
			
			$originalKeys[$name] = $key;
			$this->configs[$name] = $config;
		}
	}
	
	
	/**
	 * Expose only the config names, so var_dump/print_r never print config values.
	 */
	public function __debugInfo(): array
	{
		return [
			'configs' => array_keys($this->configs)
		];
	}
	
	public function get(string $configName): ?array
	{
		return $this->configs[$this->normalize($configName)] ?? null;
	}
}
