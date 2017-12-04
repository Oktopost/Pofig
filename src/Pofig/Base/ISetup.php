<?php
namespace Pofig\Base;


use Pofig\IConfigPath;
use Pofig\IConfigLoader;


interface ISetup
{
	/**
	 * @param IConfigPath[] ...$path
	 * @return ISetup|static
	 */
	public function addPathResolver(IConfigPath ...$path): ISetup;
	
	/**
	 * @param array $types
	 * @param array $separators
	 * @return ISetup|static
	 */
	public function addSimplePath(array $types, array $separators = ['.', '/', '\\']): ISetup;
	
	/**
	 * @param IConfigLoader[]|array ...$loader
	 * @return ISetup|static
	 */
	public function addLoader(...$loader): ISetup;
	
	/**
	 * @param string|array $type
	 * @param IConfigLoader $loader
	 * @return ISetup|static
	 */
	public function addLoaderForType($type, IConfigLoader $loader): ISetup;
}