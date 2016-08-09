var debug = false;

$(document).ready(function() {
    $(document).click(function(event) {
        //var firstMargin = parseInt($('body>*:first').css('margin-top'));
        var data = {
            pos: {
                x: event.pageX,
                //y: (event.pageY - firstMargin)
                y: event.pageY
            },
            loc: window.location.pathname
        };

        //$('<div class="punkt"></div>').css('left',data.pos.x).css('top',data.pos.y).appendTo('body');
        debug&&console.log("req post " + $.now());
        $.post("heatmap.php", data, debug&&function(textStatus) {
            console.log("req done " + $.now());
            console.log(textStatus);
        });
    });
});

function getData() {
    debug&&console.log($.now() + " start fetching data");
    $.post("heatmap.php", 'getData', function(textStatus) {
        var data = JSON.parse(textStatus);
        debug&&console.log($.now() + " received data, start to draw");
        //console.log(textStatus);
        data.forEach(function(element, index) {
            //console.log(element);
            $('<div class="punkt"></div>').css('left',element.posx+'px').css('top',element.posy+'px').appendTo('body');
        });
        debug&&console.log($.now() + " finished drawing " + data.length + " elements");
    });
    return 1;
}
