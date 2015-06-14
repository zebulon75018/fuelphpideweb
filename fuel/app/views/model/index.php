<p>
You have the choose to generate Model fuel php objet for the list of the the tables in the database.
If the check the orm then the objet will be created
</p>
<p>
Generate for table :
<input type="checkbox" name="select-all" id="select-all" /> Check all / uncheck all
</p>
<form action="model/generate">
<table>
<thead>
<tr><th>Table </th>
<th>Orm </th>
<th><!--Crud --> </th>
</tr></thead>
<tbody>
<?php
foreach($tables as $t) { 
?>
<tr> <td> <b><?php echo  $t ; ?></b> </td>
	<td><input type="checkbox" name="t[<?php echo $t ?>]"></td>
	<td><!-- <input type="checkbox" name="s[<?php echo $t ?>]"> --> </td>
	<td>
<?php
    if ( Model_Base::doesModelExists( $t ) ) {
?>
 <a href="/fuelphp/public/model/view/<?php echo  Model_base::getModelName( $t );?>" > edit config</a> </td>
<?php
	}
?>
</tr>
<?php 
	} 
?>
</tbody>
</table>
<input type="submit">
</form>


<script >
$('#select-all').click(function(event) {   
    if(this.checked) {
        // Iterate each checkbox
        $(':checkbox').each(function() {
            this.checked = true;                        
        });
    } else 
	{
           $(':checkbox').each(function() {
            this.checked = false;                        
       	 });
	}
});
</script >
