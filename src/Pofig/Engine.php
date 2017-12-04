<?php
namespace Pofig;


use Pofig\Base\IEngine;
use Pofig\Base\IMainSetup;
use Pofig\Exceptions\PofigException;
use Pofig\Setup\GroupSetup;
use Pofig\Setup\MainSetup;


class Engine implements IEngine
{
	/** @var MainSetup */
	private $setup;
	
	
	private function loadFileForGroup(GroupSetup $setup, string $fullPath, string $ext): ?array
	{
		$loaders = $setup->getLoadersFor($ext);
		
		if (!$loaders)
			return null;
		
		foreach ($loaders as $loader)
		{
			$res = $loader->load($fullPath);
			
			if (is_array($res))
			{
				return $res;
			}
		}
		
		return null;
	}
	
	private function tryLoadFile(GroupSetup $setup, string $fullPath): ?array
	{
		if (!file_exists($fullPath))
			return null;
		
		if (!is_readable($fullPath))
			throw new PofigException("File $fullPath is unreadable");
		
		$ext = pathinfo($fullPath, PATHINFO_EXTENSION);
		
		$res = $this->loadFileForGroup($setup, $fullPath, $ext);
		
		if (!is_array($res))
			$res = $this->loadFileForGroup($this->setup->getMainGroup(), $fullPath, $ext);
		
		return $res;
	}
	
	private function tryLoadGroup(GroupSetup $setup, string $configName, $files): ?array
	{
		$files = array_merge($files, $setup->getPathFor($configName));
		$files = array_unique($files, SORT_STRING);
		
		foreach ($setup->getIncludePath() as $path)
		{
			foreach ($files as $file)
			{
				$fullPath = ($file[0] == DIRECTORY_SEPARATOR) ? 
					$path . $file : 
					$path . DIRECTORY_SEPARATOR . $file;
					
				$res = $this->tryLoadFile($setup, $fullPath);
				
				if (is_array($res))
				{
					return $res;
				}
			}
		}
		
		return null;
	}
	
	
	public function __construct()
	{
		$this->setup = new MainSetup();
	}
	
	
	public function setup(): IMainSetup
	{
		return $this->setup;
	}
	
	public function parse(array $config, string $type)
	{
		if ($type == 'array')
			return $config;
		
		$parser = $this->setup->getParserFor($type);
		
		if (!$parser)
			throw new PofigException("No parser found for type '$type'");
		
		return $parser->parse($config, $type);
	}
	
	public function get(string $configName, string $type)
	{
		$merged = null;
		$files = $this->setup->getMainGroup()->getPathFor($configName);
		
		foreach ($this->setup->getGroups() as $group)
		{
			$res = $this->tryLoadGroup($group, $configName, $files);
			
			if (is_null($res))
				continue;
			
			$merged = is_null($merged) ? 
				$res : 
				array_replace_recursive($merged, $res);
		}
		
		if (is_null($merged))
			throw new PofigException("No configuration found for '$configName'");
		
		return $this->parse($merged, $type);
	}
}