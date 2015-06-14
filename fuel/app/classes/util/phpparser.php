<?php

class Util_PhpParser {
	var $_code ;
	var $_tokens ;
	var $_classname ;
	function __construct($text){
	   $this->_code = $text;
	   $this->parse();
       }
       public function parse(){

		$this->_classname = array();
		$this->_tokens = token_get_all($this->_code); 

		for ($n=0;$n< count($this->_tokens);$n++ )
		{
			$token = $this->_tokens[$n];
			if ( $token[0]== T_CLASS)
			{
			   $this->_classname[] = $this->_tokens[$n+2][1];
			}
		}
       }
       public function getClassName(){
		return $this->_classname;
	}

}; 
