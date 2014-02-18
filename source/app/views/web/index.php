<?php
if (!empty($orders)) {
    ?>
<table cellpadding="14" cellspacing="0" class="order_list">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Table No.</th>
            <th>Progress</th>
            <th>Date / Time Ordered</th>
            <th>View Order</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($orders as $id=>$order) {
            ?>
        <tr class="status_<?php echo $order['status']; ?>">
            <td align="center" width="60" class="order_id"><?php echo $id; ?></td>
            <td align="center" width="100"><?php echo $order['table']; ?></td>
            <td class="order_status_select"><input type="hidden" name="order_id" value="<?php echo $id; ?>" /><select name="order_status">
                    <?php
                    echo $this->selectList($this->orderStatuses(), $order['status']);
                    ?>
                </select>
            </td>
            <td><?php echo $order['time_ordered']; ?></td>
            <td align="center" width="150"><a href="/web/view/<?php echo $id; ?>" class="view_order_btn">View Order #<?php echo $id; ?></a></td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<?php
}
?>