function changeCheckbox(checkbox) {
    let fieldset = $(checkbox).parents('fieldset')
    let widget = $(fieldset).find('.widget')
    if (widget.length) {
        if ($(checkbox).is(':checked')) {
           widget.show('fast')
        } else {
            widget.hide('fast')
        }
    }
}

$(() => {
    $('.switch input[type=checkbox]').each(function(i, checkbox) {
        changeCheckbox(checkbox)
    }).on('change', function() {
        changeCheckbox(this)
    })
})
