<?php
class Controller_crud extends Controller_Template
{

        public function action_index()
        {

                $this->template->title = "crud";
                $this->template->content = View::forge('crud/index', null);

        }
	public function action_edit($model, $id)
	{

          $obj = $model::find($id);
          $data["obj"] = $obj;
          if ( array_key_exists("root",$_GET) ) {
		$obj->updateFromJson($_GET["root"]);
		$this->displaylist($model);
		}
	  else 
		{

        	$json = new Widget_Jsoneditor("editor_holder",$obj->getSchemaJson());
        	$json->setValue( $obj->getJsonValue());
        	$data["json"] = $json;

       		 $this->template->title = "crud/edit";
       		 $this->template->content = View::forge('crud/edit', $data);
		}
	}

	public function action_delete($model,$id)
	{
        $obj = $model::find($id);
	if ( array_key_exists("confirm",$_GET)) {
          $obj->delete();
	  $this->displaylist($model);
	} else {
        	$data["obj"] = $obj;
        	$this->template->title = "crud/delete";
        	$this->template->content = View::forge('crud/delete', $data);
	       }
	}

	public function action_create($model)
	{

          if ( array_key_exists("root",$_GET) ) {

                $obj = new $model($_GET["root"]);
		$obj->save();
		$this->displaylist($model);

	  } else {
                $obj = new $model();
          	$data["obj"] = $obj;

        	$json = new Widget_Jsoneditor("editor_holder",$obj->getSchemaJson());
        	$data["json"] = $json;

          	$this->template->title = "crud/create";
          	$this->template->content = View::forge('crud/edit', $data);
		}
	}

	public function action_view($model)
	{
         $this->displaylist($model);
	}

	public function displaylist($model)
	{
           $t = new Widget_Datatable("table", new Util_DataProvider_ModelCrud($model));
           $this->template->title = $model;
           $this->template->set("content", (string) $t, false);


	}
}
?>
