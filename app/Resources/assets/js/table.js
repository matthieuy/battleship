/* global $ */
$(() => {
  $('table.extendable thead').click(function () {
    let table = $(this).parent('table')
    $(table).find('tbody').fadeToggle('slow', function () {
      if ($(this).is(':visible')) {
        $(table).removeClass('minimize')
      } else {
        $(table).addClass('minimize')
      }
    })
  })
})
