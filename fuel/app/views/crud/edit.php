<?
  $json = new Widget_Jsoneditor("editor_holder",$obj->getSchemaJson());
  $json->setValue( $obj->getJsonValue());
?>

<form >
  <?php echo $json; ?>
   <div id="editor_holder" > </div>
        <input type="submit" />
</form>

  <?php echo $json->renderjs(); ?>
