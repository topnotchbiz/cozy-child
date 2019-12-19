<tr>
	<td class="sort">&nbsp;</td>
	<td>
		<input type="text" style="width: 100%;" name="positions[label][]" value="<?php echo isset($position) && $position ? $position : ''; ?>" placeholder="Position" />
	</td>
	<td>
		<select name="positions[size][]" style="width: 100%">
			<option value="" <?php if ( !isset($printing_size) || !$printing_size ){ echo 'selected'; } ?> disabled>Please select a size</option>
			<option value="S" <?php if ( isset($printing_size) && $printing_size == 'S' ){ echo 'selected'; } ?>>Small</option>
			<option value="L" <?php if ( isset($printing_size) && $printing_size == 'L' ){ echo 'selected'; } ?>>Large</option>
		</select>        
	</td>
	<td class="remove">&times;</td>
</tr>