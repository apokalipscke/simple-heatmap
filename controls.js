window.onunload = refreshParent;

function refreshParent() {
    window.opener.$('#drawing').css('display', 'none');
    window.opener.$('svg').remove();
}

function changeLocation(location) {
    window.opener.location = location;
    window.location = 'controls.php?loc=' + location;
}

function changeWidth(width) {
    window.opener.$('svg').remove();
    window.opener.getData();
    window.opener.$('body').css('width', width-33 + 'px');
    window.opener.$('svg').css('width', width-33 + 'px');
}
