<?php
namespace Pofig\Base;


use Pofig\Objects\ReferenceConfigObject;


interface IConfigObject
{
	/**
	 * @param string $name
	 * @return mixed|ReferenceConfigObject
	 */
	public function __get($name);
	
	/**
	 * Troughs exception
	 * @param $name
	 * @param $value
	 */
	public function __set($name, $value);
	
	
	/**
	 * @param string[] $with
	 * @return IConfigObject|static
	 */
	public function merge(string ...$with): IConfigObject;
	
	/**
	 * @param string $type
	 * @return mixed
	 */
	public function get(string $type = 'array');
	
	/**
	 * @return ReferenceConfigObject
	 */
	public function getReferenceObject(): ReferenceConfigObject;
	
	public function toArray(): array;
}