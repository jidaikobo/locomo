<h2>Viewing #<?php echo $item->id; ?></h2>

<table class="tbl">

<tr>
	<th>受付日(入金日)</th>
	<td><?php echo $item->receipt_at; ?></td>
</tr>

<tr>
	<th>customer_id</th>
	<td><?php echo $item->customer_id; ?></td>
</tr>

<tr>
	<th>support_type</th>
	<td><?php echo $item->support_type; ?></td>
</tr>

<tr>
	<th>subject_id</th>
	<td><?php echo $item->subject_id; ?></td>
</tr>

<tr>
	<th>support_money</th>
	<td><?php echo $item->support_money; ?></td>
</tr>

<tr>
	<th>fee</th>
	<td><?php echo $item->fee; ?></td>
</tr>

<tr>
	<th>support_article</th>
	<td><?php echo $item->support_article; ?></td>
</tr>

<tr>
	<th>article_delivery_gid</th>
	<td><?php // echo $item->article_delivery_gid; ?></td>
</tr>

<tr>
	<th>consignee_type</th>
	<td><?php echo $item->consignee_type; ?></td>
</tr>

<tr>
	<th>support_aim</th>
	<td><?php echo $item->support_aim; ?></td>
</tr>

<tr>
	<th>memo</th>
	<td><?php echo $item->memo; ?></td>
</tr>

<tr>
	<th>is_letter_of_thanks</th>
	<td><?php echo $item->is_letter_of_thanks ? 'Yes' : 'No' ; ?></td>
</tr>

<tr>
	<th>send_letter_of_thanks_at</th>
	<td><?php echo $item->send_letter_of_thanks_at; ?></td>
</tr>

<tr>
	<th>classification</th>
	<td><?php echo $item->classification; ?></td>
</tr>

<tr>
	<th>entry_at</th>
	<td><?php echo $item->entry_at; ?></td>
</tr>

<tr>
	<th>entry_user</th>
	<td><?php echo $item->entry_user; ?></td>
</tr>

<tr>
	<th>entry_uid</th>
	<td><?php echo $item->entry_uid; ?></td>
</tr>

<tr>
	<th>update_at</th>
	<td><?php echo $item->updated_at; ?></td>
</tr>

<tr>
	<th>update_user</th>
	<td><?php echo $item->update_user; ?></td>
</tr>

<tr>
	<th>update_uid</th>
	<td><?php echo $item->update_uid; ?></td>
</tr>

<tr>
	<th>deleted_at</th>
	<td><?php echo $item->deleted_at; ?></td>
</tr>

<tr>
	<th>is_contribuer</th>
	<td><?php echo $item->is_contributer ? 'Yes' : 'No' ; ?></td>
</tr>


</table>

