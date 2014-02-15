var Modal = function(element){
    // get data
    var modal = this;
    modal.active = false;
    modal.showBG = element.background;
    modal.width = $(window).width() - 200;
    modal.height = $(window).height() - 200;
    if (typeof element.href !== 'undefined') {
        modal.content = '<div class="loader_centre"><p class="large_text">Order loading</p><div id="movingBallG"><div class="movingBallLineG"></div><div id="movingBallG_1" class="movingBallG"></div></div>';
        modal.href = element.href;
    } else {
        modal.content = element.content;
    }
    modal.show();
};
Modal.prototype.show = function(){
    var modal = this;
    if(!modal.active){
        modal.active = true;
        // make ui
        modal.modal = $('<div class="modalContainer"><div class="modalBackground"></div><div class="modalPopup"><div class="modalClose"></div><div class="modalContent">'+modal.content+'</div></div></div>');
        $('.modalClose',modal.modal).on('click', function(){
            modal.remove();
        });
        $('.modalBackground', modal.modal).on('click', function(){
            modal.remove();
        });
        $('body').append(modal.modal);
        if(!modal.showBG){
            $('.modalBackground', modal.modal).hide();
        }else{
            $('.modalBackground', modal.modal).css({
                position:'absolute',
                opacity:0.5,
                background: '#000',
                top:0,
                left:0
            });
            var resize = function(){
                $('.modalBackground', modal.modal).css({
                    width:$(window).width(),
                    height:$(window).height()
                });
            };
            resize();
            $(window).on('resize', function(){resize();});
        }
        // position modal in the center
        modal.modal.css({
            opacity:0
        }).animate({
            opacity:1
        }, 250);
        var posTop = $(window).height() / 2 - modal.height / 2;
        if (posTop < 0) {
            posTop = 0;
        }
        $('.modalPopup', modal.modal).css({
            top:posTop,
            left:$(window).width() / 2 - modal.width / 2,
            width: modal.width - 40,
            height: modal.height - 40
        });
        $('.modalContent', modal.modal).css({
            width: modal.width - 40,
            height: modal.height - 40
        });
        if (typeof modal.href !== 'undefined') {
            $.ajax({
                'url': modal.href,
                'success': function(html) {
                    $(".modalContent", modal.modal).html(html);
                }
            });
        }
    }
};
Modal.prototype.remove = function(){
    this.modal.stop(true,true).animate({opacity:0}, 250, function(){$(this).remove();});
    this.active = false;
};