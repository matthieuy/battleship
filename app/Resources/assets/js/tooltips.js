/***********
 * Tooltip *
 ***********/
var moveTooltip = function(e) {
   let height = $('div.tooltip').outerHeight();
    $('div.tooltip').css({
	top: (e.pageY-height-8),
	left: (e.pageX)
    });
};
var openTooltip = function(e) {
    $('div.tooltip').remove();
    $('<div class="tooltip">'+$(this).attr('data-tip')+'</div>').appendTo('body');
    moveTooltip(e);
};
var hideTooltip = function() {
    $('div.tooltip').remove();  
};

$(document)
    .on('mouseenter', '.opentip', openTooltip)
    .on('mousemove', '.opentip', moveTooltip)
    .on('mouseleave', '.opentip', hideTooltip);
