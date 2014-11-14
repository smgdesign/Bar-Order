<?php
if (isset($order)) {
    ?>
    <table cellpadding="10" class="order_info" cellspacing="0">
        <tr>
            <th>Order No:</th>
            <td><?php echo $order['id']; ?></td>
            <th>Progress:</th>
            <td><?php echo $this->orderStatus($order['status']); ?></td>
            <th rowspan="2" valign="top">Order Notes:</th>
            <td width="33%" rowspan="2" valign="top"><textarea name="instruction" class="order_instruction"><?php echo $order['instruction']; ?></textarea></td>
        </tr>
        <tr>
            <th>Table No:</th>
            <td><?php echo $order['table']; ?></td>
            <th>Total:</th>
            <td>&pound;<?php echo number_format($order['total'], 2); ?></td>
        </tr>
    </table>
<h2>Ordered Items:</h2>
<table cellpadding="10" class="order_items" cellspacing="0">
    <thead>
        <tr>
            <th>Item</th>
            <th align="center">Quantity</th>
            <th align="center">Price</th>
            <th>Edit</th>
        </tr>
    </thead>
    <tbody>
<?php
$odd = true;
if (is_array($order['items'])) {
    foreach ($order['items'] as $id=>$item) {
        ?>
            <tr id="item_<?php echo $id; ?>" class="<?php echo ($odd) ? 'row_odd' : 'row_even'; ?>">
                <td><?php echo $item['title']; ?></td>
                <td width="17%" align="center"><?php echo $item['qty']; ?></td>
                <td width="17%" align="center">&pound;<?php echo $item['price']; ?></td>
                <td width="110" align="center"><a href="/web/order/edit/<?php echo $id; ?>" class="edit_btn">Edit</a></td>
            </tr>
    <?php
        if ($odd) {
            $odd = false;
        } else {
            $odd = true;
        }
    }
}
?>
    </tbody>
</table>
<?php
} else if (isset($error)) {
    echo '<p class="error">'.$error.'</p>';
}
?>