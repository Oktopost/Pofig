<?php
namespace Pofig\Base;


interface IConfig
{
	public function clearCache(): void;
	
	/**
	 * @param string $name
	 * @param string $type
	 * @return mixed
	 */
	public function get(string $name, string $type = 'array');
	
	/**
	 * @param string $name
	 * @return IConfigObject
	 */
	public function getConfigObject(string $name): IConfigObject;
}