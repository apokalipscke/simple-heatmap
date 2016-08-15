var debug = true;

$(document).ready(function() {

    $(document).on("click", function(event) {
        //var firstMargin = parseInt($('body>*:first').css('margin-top'));
        var data = {
            pos: {
                x: event.pageX,
                //y: (event.pageY - firstMargin)
                y: event.pageY
            },
            dim: {
                  innerWidth: window.innerWidth,
                 innerHeight: window.innerHeight,
                 screenWidth: screen.width,
                screenHeight: screen.height,
            },
            loc: window.location.pathname
        };

        if(debug) {
            $('<div class="punkt"></div>').css('left',data.pos.x).css('top',data.pos.y).appendTo('body');
            console.log("req post " + $.now());
            $.post("heatmap.php", data, function(textStatus) {
                console.log("req done " + $.now());
                console.log(data);
                console.log(textStatus);
            });
        }
    });
});

function drawOverlay(d) {
    var draw = SVG('drawing').size(document.body.scrollWidth, document.body.scrollHeight);
    var gradient = draw.gradient('radial', function(stop) {
        stop.at(0   , '#04A4EE', 0.05);
        stop.at(1   , '#04A4EE', 0);
    });
    //var circle = draw.circle(100).move(100, 100).fill(gradient);
    //var circles = [];
    var diameter = 50;
    d.forEach(function(element, index) {
        draw.circle(diameter).move(element.posx-diameter/2, element.posy-diameter/2).fill(gradient);
        //console.log(element.posx)
    });
}

function getData() {
    $('#drawing').show();
    debug&&console.log($.now() + " start fetching data");
    $.post("heatmap.php", 'getData='+window.location.pathname, function(textStatus) {
        var data = JSON.parse(textStatus);
        debug&&console.log($.now() + " received data, start to draw");
        //console.log(textStatus);
        //data.forEach(function(element, index) {
            //console.log(element);
            //$('<div class="punkt"></div>').css('left',element.posx+'px').css('top',element.posy+'px').appendTo('body');
        //});
        drawOverlay(data);
        debug&&console.log($.now() + " finished drawing " + data.length + " elements");
    });
    return 1;
}
