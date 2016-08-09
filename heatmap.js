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

    $.post( "heatmap.php", data, function(textStatus) {
      console.log(textStatus);
    });
  });
});

function getData() {
    $.post("heatmap.php", 'getData', function(textStatus) {
        var data = JSON.parse(textStatus);
        data.forEach(function(element, index) {
            $('<div class="punkt"></div>').css('left',element[0]+'px').css('top',element[1]+'px').appendTo('body');
        });
    });
}
