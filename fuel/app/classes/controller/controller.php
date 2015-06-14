<?php
if (!defined('CONTROLLER')) {

define('CONTROLLER', 1);


class controllerManager {


	public function __construct()
       {
       }

	static public function getlist() {
	   $result = array();
	   $ac = glob(APPPATH."/classes/controller/*.php");
	   foreach($ac as $c ) {

		include $c;
		$phpparser = new Util_PhpParser(file_get_contents($c));
	        $className = $phpparser->getClassName();
	        $result[  str_replace(".php","", str_replace(APPPATH."/classes/controller/","",$c)) ] = array();
	        foreach( $className as $cn )
                {
	         foreach(  get_class_methods($cn)  as $m ) 
		 {
                   if ( strpos($m,"action_") !==false ||  strpos($m,"get_" ) !==false ||  strpos($m,"post_" ) !==false )
                    {
                      	$result[ str_replace(".php","",str_replace(APPPATH."/classes/controller/","",$c)) ] [] = $m;
		    }
		 }
                }
           }
	   return $result;
	}

        public function getFilename($name) {
           return  APPPATH."/classes/controller/".$name.".php";
	}

	public function isControllerExist($name)
        {
	  $split = explode("/",$name);
          if ( count( $split ) > 1 ) {
	     $path = "";
             foreach( $split as $s ) {
	        if ($path!="") 
		{
		   $path.="/$s";
		} else {
		   $path.=$s;
		}
		if ( file_exists($this->getFilename( $path ))) {
			return true;
		}
	     }
	  } else {
		
		if ( file_exists($this->getFilename( $name ))) {
			return true;
		} else {
			return false;
		}
          }
        }

	public function generate($name) {
		if ($this->isControllerExist($name) )
		{
	          $split = explode("/",$name);
		  $this->addMethod($split[0],$split[1]);
		} else
		{
		  $this->generateNewFile($name);
		}
	}


	public function addMethod($name,$method) {
             $file= $this->getFilename($name);
	     $content = file_get_contents($file);
             $result = "";
	     for($n= strlen( $content)-1 ; $n > 0 ; $n--)
	     {
	        if ( $content[$n] =="}" )
                {
		 $result = substr($content,0, $n-1 );
		$result.=<<<EOD

	public function action_$method()
	{
        \$this->template->title = "$name/$method";
        \$this->template->content = View::forge('$name/$method', null);
	}
}
?>
EOD;

		break;
		}
            }
	 if ( $result!="") {
                $this->generatefile($name,$method,$result);
                //$this->createfile($file,$result);
	 }
	}

		
	public function generateNewFile($name) {
         $file= $this->getFilename($name);

	$result=<<<EOD
<?php
class Controller_$name extends Controller_Template
{

        public function action_index()
        {
                \$this->template->title = "$name";
                \$this->template->content = View::forge('$name/index', null);

        }
}
?>
EOD;

          $this->generatefile($name,"index",$result);

	}

	function generatefile($name, $method ,$result)
        {
           $file= $this->getFilename($name);
           $this->createfile($file,$result);
           $fileview= APPPATH."/views/".$name."/";
	   if ( is_dir($fileview) == false ) {
	   	mkdir($fileview);
	   }
           $fileview= $fileview."/$method.php";
	   file_put_contents ($fileview,"");

        }

        public function createfile($file, $content)
        {
	   file_put_contents ($file,$content);
        } 
}

class Controller_Controller extends Controller_Template
{

	public function action_index()
	{
		$data['controller'] = controllerManager::getlist();
		$this->template->title = "Controller";
		$this->template->content = View::forge('controller/index', $data);

	}

	public function action_add() 
        {
		$c = $_GET["controler"];
                $cm = new controllerManager();
		$cm->generate($c);
		$data['controller'] = controllerManager::getlist();
		$this->template->title = "Controller";
		$this->template->content = View::forge('controller/index', $data);
        }

	public function action_editview($name = null)
	{
		$re = "/([a-zA-Z0-9]*):([a-zA-Z0-9]*)_([a-zA-Z0-9]*)/"; 

		if (preg_match($re, $name, $matches))  {
	
			$controllername=$matches[1];
			$methode=$matches[3];
		
			if (array_key_exists("html",$_GET) )
       	       		{
			   file_put_contents(APPPATH."/views/".$controllername."/".$methode.".php",$_GET["html"]);
			}

			$data['controller'] = file_get_contents(APPPATH."/views/".$controllername."/".$methode.".php");
			$this->template->title = "Edit View Controller $name ";
			$this->template->content = View::forge('controller/editview', $data,false);
		} else {

			$data['controller'] = "";
			$this->template->title = "Edit View Controller $name ";
			$this->template->content = View::forge('controller/editview', $data,false);
		}

	}

}
}
?>
