// Require
require('jquery')

// Flash message
var FlashSystem = require('./Flash')
Flash = new FlashSystem()

// WebSocket
WS = require('./WS.js')(WS_URI);

// Document.ready
$(() => {
    // Sidebar
    let sidebar = require('./Sidebar')
    sidebar.init()
})


/**
 * Check if a variable exist
 * @param variable
 * @returns {boolean}
 */
function isset(variable) {
    return (typeof variable !== 'undefined');
}