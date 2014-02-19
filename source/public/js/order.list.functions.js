/* 
 * Bar App - 2014
 */
var lastSync = 'first';
var Poll = function() {
    var poll = this;
    $.ajax({
        'url': '/web/orders/list/',
        'data': {'last': lastSync},
        'dataType': 'json',
        'type': 'post',
        'success': function(i) {
            if (i.status.code === 3) {
                var dataL = i.data.length;
                if (isNaN(dataL)) {
                    for (var x in i.data) {
                        $(".order_list tbody").prepend('<tr id="order_'+x+'" class="status_'+i.data[x].status+'"><td align="center" width="60" class="order_id">'+x+'</td><td align="center" width="100">'+i.data[x].table+'</td><td class="order_status_select"><input type="hidden" name="order_id" value="'+i.data[x].id+'" /><select name="order_status"></select></td><td>'+i.data[x].time_ordered+'</td><td align="center" width="150"><a href="/web/view/'+x+'" class="view_order_btn">View Order #'+x+'</a></td></tr>');
                        $("select", "#order_"+x).append('<option value="0">Order Placed</option><option value="1">In Progress</option><option value="2" selected="selected">Serving</option><option value="3">Completed</option><option value="4">Problem with Order</option>');
                        $("select", "#order_"+x).val(i.data[x].status);
                    }
                }
            }
            if (typeof i.lastSync !== 'undefined') {
                lastSync = i.lastSync;
            }
            setTimeout(function() {
                Poll();
            }, 5000);
        }
    });
};

$(function() {
    Poll();
    $(".order_list").on('change', "select[name='order_status']", function() {
        var $this = $(this),
            $row = $(this).parent().parent();
        $.ajax({
            'url': '/api/change_status/order',
            'type': 'post',
            'dataType': 'json',
            'data': {'id': $this.prev('input').val(), 'status': $this.val()}
        }).done(function(i) {
            if (i.status.code === 4) {
                $row.removeClass().addClass('status_'+$this.val());
            }
        });
    });
    $(".order_list").on({
        'mouseup': function() {
            new Modal({
                'href': $(this).prop('href'),
                'background': true
            });
        },
        'click': function(ev) {
            ev.preventDefault();
        },
        'mouseenter': function() {
            $(this).fadeTo("250", 0.8);
        },
        'mouseleave': function() {
            $(this).fadeTo("250", 1);
        }
    }, "a.view_order_btn");
    $("body").on({
        'mouseup': function() {
            if ($(this).hasClass('save_btn')) {
                $(this).removeClass('save_btn');
                $(this).text('Edit');
            } else {
                $(this).addClass('save_btn');
                $(this).text('Save');
            }
        },
        'click': function(ev) {
            ev.preventDefault();
        },
        'mouseenter': function() {
            $(this).fadeTo("250", 0.8);
        },
        'mouseleave': function() {
            $(this).fadeTo("250", 1);
        }
    }, 'a.edit_btn');
    
    $("body").on('change', "select[name='item_status']", function() {
        var $this = $(this),
            $td = $(this).parent();
        $.ajax({
            'url': '/api/change_status/item',
            'type': 'post',
            'dataType': 'json',
            'data': {'id': $("input[name='order_id']", $this.parent()).val(), 'item_id': $("input[name='item_id']", $this.parent()).val(), 'status': $this.val()}
        }).done(function(i) {
            if (i.status.code === 4) {
                $td.removeClass().addClass('status_'+$this.val());
            }
        });
    });
});
