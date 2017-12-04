<?php
namespace Pofig\Base;


interface IEngine
{
	/**
	 * @param array $config
	 * @param string $targetType
	 * @return mixed
	 */
	public function parse(array $config, string $targetType);
	
	/**
	 * @param string $configName
	 * @param string $type
	 * @return mixed
	 */
	public function get(string $configName, string $type);
}