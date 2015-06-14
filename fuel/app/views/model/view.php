<?
     $genericschema ='{
      "type": "object",
        properties: {
          formatdisplay: { "type": "string", "enum":["grid","onecolumn"],"default":"grid" } ,
          display: { "type": "string" },		  
       "fields" : {
      "options" : {
		"disable_array_add" : true,
		"disable_array_delete" : true,
		"disable_array_reorder": true,
	},
      "type": "array",
      "format": "table",
      "title": "TABLE EDITOR",
      "uniqueItems": true,
      "items": {
        type: "object",
        properties: {
          name: { "type": "string" },		  
          label: { "type": "string" },		  
	 "type": {
            "type": "string",
            "enum": [
              "INT",
              "VARCHAR",
	      "CHAR",
	      "TEXT",
              "DATE",
              "BOOLEAN",
              "TIMESTAMP",
	      "DECIMAL",
	      "COLOR",
	      "ICON",
	      "PRIMARYKEY",
	      "FOREIGNKEY"
            ],
            "default": "INT"            		  
		  },
          reftable: { "type": "string" , 
                "default": "",
		"enum" : '. json_encode(Util_OrmFromDB::getAllTableAndNone()).'
		},
          show: { 
  		"type": "boolean",
                "default": "true"            		  
		}
	  }
        }
      }
    }
  }';
  $json = new Widget_Jsoneditor("editor_holder",$genericschema);
  $json->setValue( $schema);
  echo Asset::js("jsoneditor.js");
?>

<form >
  <?php echo $json; ?>
   <div id="editor_holder" > </div>
        <input type="submit" />
</form>

  <?php echo $json->renderjs(); ?>

<?php /*
<script>
JSONEditor.defaults.options.disable_array_reorder = true;
JSONEditor.defaults.options.disable_array_delete = true;
JSONEditor.defaults.options.theme = "bootstrap3";
	 // Initialize the editor
</script> */ ?>
