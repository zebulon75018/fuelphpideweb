<?php
class Controller_model extends Controller_Template
{

        public function action_index()
        {
		$ormfromdb = new Util_OrmFromDB();
                $mysqli = $ormfromdb->getmysqli();
		$data["tables"] =  $ormfromdb->getTable( $mysqli );

                $this->template->title = "model";
                $this->template->content = View::forge('model/index', $data);

        }

        public function action_view( $model ) {
	        $m = new $model();
		if ( array_key_exists("root",$_GET) ) {
		    $m->saveJsonSchema( $_GET["root"] );
		}
		$data["schema"] = $m->getjsonschema();

                $this->template->title = $model;
                $this->template->content = View::forge('model/view',  $data, false);
	}

	public function action_filter( $model )
	{
		$m = new $model();
		if ( array_key_exists("json",$_POST) )
		{
		  $data["json"] = $_POST["json"];
		  $whereSql = Model_Base::jsonFilterToSql( json_decode( $_POST["json"] ) );
		  $dp = new Util_DataProvider_Modelsql( $model , $m->getSqlString( $whereSql ) );
		  $table = new Widget_Table( "resultfilter", $dp );
		  $table->setClass("table");
		  $data["widget"] = $table;
                  return new Response(View::forge('model/filterresult', $data,false));
		} 
		else
		{
                  $f = new Widget_Jsonfilter("filter");
		  $f->setfilterjson($m->getjsonschema());
                  $data["widget"] = $f;
        	  $this->template->title = "model/filter";
        	  $this->template->content = View::forge('model/filter', $data,false);
		}
	}

       public function action_importfromdb ()
       {
		$ormfromdb = new Util_OrmFromDB();
		$ormfromdb->run();
                $this->template->title = "model import from DB";
                $this->template->content = View::forge('model/importfromdb', null);
       }
	public function action_generate()
	{

		$ormfromdb = new Util_OrmFromDB();
                $mysqli = $ormfromdb->getmysqli();
		$data['result'] = array();
		if ( array_key_exists("t", $_GET ) ) {
			$tables = $_GET["t"]; 
			foreach( array_keys ( $tables ) as $t ) {
		   		if ( $ormfromdb->generate_model($t,$mysqli) ) {
		   			$data['result'][] =   $t;
				}
			}
		}
		if ( array_key_exists("s", $_GET ) ) {
				$crud = $_GET["s"]; 
			}
		  $this->template->title = "model/generate";
        	$this->template->content = View::forge('model/generate', $data);
	}
}
?>
