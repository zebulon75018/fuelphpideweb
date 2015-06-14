<?php

/* 
 * Builder dataprovider from a Fuel Model .
 */
class Util_DataProvider_Model extends Util_DataProvider_Base {

  protected $_model = null;
  protected $_objmodel = null;

  function __construct( $model) {
    $this->_model = $model;
    $this->_objmodel = new $model();
  }

  function nbColumns() 
  {
      count($this->_model->getShowingFields());
  }

  function nbRows()
  {
    //$this->_model->query("select count(id) ");
    return 0;
  }

  function getColumnsLabel()
  {
    $result = array();
    foreach( $this->_objmodel->getShowingFields() as $p )
    {
       $result[] = $p;
    }
    return $result;
  }

  function getData()
  {
    $result = $this->_objmodel->find("all");
    return $result;
  }

  function getFields() {
    $result = array();
    foreach( $this->_objmodel->getShowingFields() as $key) {
       $result[] = $key;
    }
    return $result;
  }

  function isFieldExternalTable( $f ) {
	return $this->_objmodel->isExternalTable( $f );
  }
}

?>
