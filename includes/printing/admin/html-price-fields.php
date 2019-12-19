<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<tr>
	<td class="sort">&nbsp;</td>
    <td>
      <select name="prices[<?php echo $key; ?>][size][]" style="width: 100%">
				<option value="S" <?php if ( isset($size) && $size == 'S' ){ echo 'selected'; } ?>>Small</option>
				<option value="L" <?php if ( isset($size) && $size == 'L' ){ echo 'selected'; } ?>>Large</option>
      </select>
		</td>
		<td>
			<input type="text" style="width: 100%;" name="prices[<?php echo $key; ?>][qty_from][]" value="<?php echo isset($qty_from) && $qty_from ? $qty_from : ''; ?>" placeholder="From" />
		</td>
    <td>
			<input type="text" style="width: 100%;" name="prices[<?php echo $key; ?>][qty_to][]" value="<?php echo isset($qty_to) && $qty_to ? $qty_to : ''; ?>" placeholder="To" />
		</td>
	    <td>
			<input type="text" style="width: 100%;" name="prices[<?php echo $key; ?>][setup_cost][]" value="<?php echo isset($setup_cost) && $setup_cost ? $setup_cost : ''; ?>" placeholder="0.00" />
		</td>
    <td>
			<input type="text" style="width: 100%;" name="prices[<?php echo $key; ?>][pos_cost][]" value="<?php echo isset($pos_cost) && $pos_cost ? $pos_cost : ''; ?>" placeholder="0.00" />
		</td>
    <td class="remove">×</td>
</tr>