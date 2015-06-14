<h2>Listing Controllers</h2>
<br>
<?php if ($controller): ?>
<table class="table table-striped">
        <thead>
                <tr>
                        <th>Name</th>
                        <th></th>
                </tr>
        </thead>
        <tbody>
<?php foreach ($controller as $key=>$item): ?>             <tr>
			<td><b><?echo $key;?></b></td>
			</tr>
			<?php foreach ( $item as $i ) { ?>
			<tr>		
                        <td><i><?php echo $key.":". $i; ?></i></td>
                        <td>
                                <?php echo Html::anchor('/controller/editview/'.$key.":".$i, 'Edit View'); ?> 
                                <?php /*echo Html::anchor('users/edit/'.$i, 'Edit');*/ ?> 
                                <?php /*echo Html::anchor('users/delete/'.$i, 'Delete', array('onclick' => "return confirm('Are you sure?')"));*/ ?>

                        </td>
                       </tr>
	   <?php } ?>
<?php endforeach; ?>    </tbody>
</table>

<?php else: ?>
<p>No Contoller.</p>

<?php endif; ?><p>

</p>

