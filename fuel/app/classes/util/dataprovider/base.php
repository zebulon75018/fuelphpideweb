<?php

abstract class Util_Dataprovider_Base {

	abstract function nbColumns(); 
	abstract function nbRows(); 
	abstract function getColumnsLabel(); 
	abstract function getData(); 
}
?>
