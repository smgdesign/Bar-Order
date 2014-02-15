<?php
$venues = '';
$locations = '';
$tables = '';
$menu = '';
$orders = array();
$tableID = 0;
if (isset($data) && !empty($data) && !isset($data->sync)) {
    if ($data->status->code == 4) {
        foreach ($data->data as $item=>$section) {
            if ($section->status->code == 3) {
                foreach ($section->data as $value) {
                    switch ($item) {
                        case "venues":
                            $venues .= '<li>'.$value->title.'</li>';
                            break;
                        case "locations";
                            $locations .= '<li>'.$value->title.'</li>';
                            break;
                        case "tables":
                            $tables .= '<li>'.$value->name.'</li>';
                            break;
                        case "menu":
                            $ingredients = array();
                            foreach ($value->ingredients as $ingredient) {
                                $ingredients[] = $ingredient->title;
                            }
                            $categories = array();
                            foreach ($value->categories as $category) {
                                $categories[] = $category->title;
                            }
                            $menu .= '<li>'.$value->title.' '.((!empty($ingredients)) ? '<br />Ingredients:<ul><li>'.implode('</li><li>', $ingredients).'</li></ul>' : '').' '.((!empty($categories)) ? 'Categories:<ul><li>'.implode('</li><li>', $categories).'</li></ul>' : '').'</li>';
                            break;
                    }
                }
                if ($item == 'menu') {
                    $orders = $section->data;
                }
                if ($item == 'tables') {
                    $tableID = $section->data[0]->id;
                }
            }
        }
    }
}
?>
<h4>Venues</h4>
<ul>
    <?php echo $venues; ?>
</ul>
<h4>Locations</h4>
<ul>
    <?php echo $locations; ?>
</ul>
<h4>Tables</h4>
<ul>
    <?php echo $tables; ?>
</ul>
<h4>Menu</h4>
<ul>
    <?php echo $menu; ?>
</ul>
<?php
if (isset($order)) {
    print_r('<pre>');
    print_r($order);
    print_r('</pre>');
}
?>
<form action="/web/test" method="post">
    <input type="hidden" name="submitted" value="TRUE" />
    <input type="hidden" name="table_id" value="<?php echo $tableID; ?>" />
    <textarea name="instructions"></textarea>
    <input type="hidden" name="time_ordered" value="<?php echo date('Y-m-d H:i:s'); ?>" />
    <ul>
    <?php
    foreach ($orders as $id=>$item) {
        echo '<li>'.$item->title.' Quantity: <input size="4" type="text" name="items['.$id.']" /></li>';
    }
    ?>
    </ul>
    <input type="submit" name="place" value="Place Order" />
</form>