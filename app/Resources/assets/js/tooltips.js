/***********
 * Tooltip *
 ***********/
/* global $ */
let moveTooltip = function (e) {
  let height = $('div.tooltip').outerHeight()
  $('div.tooltip').css({
    top: (e.pageY - height - 8),
    left: (e.pageX),
  })
}

let openTooltip = function (e) {
  $('div.tooltip').remove()
  $('<div class="tooltip">' + $(this).attr('data-tip') + '</div>').appendTo('body')
  moveTooltip(e)
}

let hideTooltip = function () {
  $('div.tooltip').remove()
}

$(document)
  .on('mouseenter', '.opentip', openTooltip)
  .on('mousemove', '.opentip', moveTooltip)
  .on('mouseleave', '.opentip', hideTooltip)
