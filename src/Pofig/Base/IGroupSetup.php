<?php
namespace Pofig\Base;


interface IGroupSetup extends ISetup 
{
	public function addIncludePath(string ...$paths): IGroupSetup;
}