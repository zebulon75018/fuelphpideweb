<?php

/* 
 * Builder dataprovider from a Fuel Model .
 */
class Util_DataProvider_ModelCrud extends Util_DataProvider_Model {

  private function createUrl($action, $id , $icon) 
  {
    return "<a href='/fuelphp/public/crud/".$action."/".$this->_model."/".$id."' class='glyphicon-".$action."'><i class='glyphicon glyphicon-".$icon."'></i> </a>";
  }

  function getData()
  {
    $resultdb = $this->_objmodel->find("all");
    $result = array();
    foreach( $resultdb as $r ) {
	$r["crudaction"] = 
		$this->createUrl("edit",$r["id"],"edit").
		$this->createUrl("delete",$r["id"],"trash");
	$result[] = $r;
    }
    return $result;
  }

  function getFields() {
    $result = array();
    $result[] = "crudaction";
    foreach( $this->_objmodel->getShowingFields() as $key) {
       $result[] = $key;
    }
    return $result;
  }

  function getColumnsLabel()
  {
	$a = parent::getColumnsLabel();
        $result = array();
        $result[] = "crudaction";
        return array_merge($result,$a);
  }
}

?>
