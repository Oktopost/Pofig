<?php
namespace Pofig\Setup;


use Pofig\IConfigPath;
use Pofig\IConfigLoader;
use Pofig\Base\ISetup;
use Pofig\Base\IGroupSetup;
use Pofig\Path\SimplePath;


class GroupSetup implements IGroupSetup
{
	/** @var IConfigPath[] */
	private $pathResolve = []; 
	
	/** @var IConfigLoader[] */
	private $loaders = [];
	
	/** @var IConfigLoader[][] */
	private $loaderPerType = [];
	
	/** @var string[] */
	private $includePath = [];
	
	
	public function addPathResolver(IConfigPath ...$path): ISetup
	{
		$this->pathResolve[] = array_merge($this->pathResolve, $path);
		return $this;
	}
	
	public function addSimplePath(array $types, array $separators = ['.', '/', '\\']): ISetup
	{
		$this->pathResolve[] = new SimplePath($types, $separators);
		return $this;
	}
	
	public function addLoader(...$loaders): ISetup
	{
		foreach ($loaders as $loader)
		{
			if (!is_array($loader))
			{
				$this->loaders = array_merge($this->loaders, $loader);
			}
			else if (isset($loader[0]))
			{
				$this->loaders = array_merge($this->loaders, $loader);
			}
			else
			{
				$this->loaderPerType = array_merge(
					$this->loaderPerType,
					$loader
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * @param string|array $type
	 * @param IConfigLoader $loader
	 * @return ISetup
	 */
	public function addLoaderForType($type, IConfigLoader $loader): ISetup
	{
		if (!isset($this->loaderPerType[$type]))
		{
			$this->loaderPerType[$type] = [$loader];
		}
		else
		{
			$this->loaderPerType[$type][] = $loader;
		}
		
		return $this;
	}
	
	public function addIncludePath(string ...$paths): IGroupSetup
	{
		foreach ($paths as $path)
		{
			$length = strlen($path);
			
			if ($path[$length - 1] == DIRECTORY_SEPARATOR)
			{
				$path = substr($path, 0, $length - 1);
			}
			
			$this->includePath[] = $path;
		}
		
		return $this;
	}
	
	public function getPathFor(string $configName): array 
	{
		$names = [];
		
		foreach ($this->pathResolve as $resolve)
		{
			$names = array_merge($names, $resolve->getFilePath($configName));
		}
		
		return $names;
	}
	
	/**
	 * @return string[]
	 */
	public function getIncludePath(): array 
	{
		return $this->includePath;
	}
	
	/**
	 * @param string $fileType
	 * @return IConfigLoader[]
	 */
	public function getLoadersFor(string $fileType): array
	{
		$typedLoaders = $this->loaderPerType[$fileType] ?? [];
			
		if (!is_array($typedLoaders))
			$typedLoaders = [$typedLoaders];
		
		if (!$this->loaders)
		{
			return $typedLoaders;
		}
		else if (!$typedLoaders)
		{
			return $this->loaders;
		}
		else
		{
			return array_merge($typedLoaders, $this->loaders);
		}
	}
}