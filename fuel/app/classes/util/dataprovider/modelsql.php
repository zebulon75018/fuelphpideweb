<?php

/* 
 * Builder dataprovider from a Fuel Model .
 */
class Util_DataProvider_Modelsql extends Util_DataProvider_Model {

  protected $_model = null;
  protected $_objmodel = null;
  protected $_sql = null;

  function __construct( $model,$sql) {
    $this->_model = $model;
    $this->_objmodel = new $model();
    $this->_sql = $sql;
  }

  function getData()
  {
    $result = DB::query($this->_sql)->as_object($this->_model)->execute(); 
    //$result = DB::query($this->_sql)->execute()->as_array(); 
    //print_r($result);
    return $result;
  }
}

?>
