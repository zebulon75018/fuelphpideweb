<div id="summernote"><?php echo  $controller; ?></div>

<form>
<textarea name="html" class="cleditor" rows="4">
<?php echo $controller; ?>
</textarea >
<input type=submit />
</form>
<script type="text/javascript" src="/fuelphp/public/assets/js/summernote.js"></script>
<link id="base-style-responsive" type="text/css" rel="stylesheet" href="/fuelphp/public/assets/css/summernote.css" />


<script>
        $(document).ready(function () { $("#summernote").summernote(); });
</script>
