/* 
 * Bar App - 2014
 */

$(function() {
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
