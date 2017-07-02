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

if ( !Date.prototype.toISOString ) {
    (function() {
        function pad(number) {
            let r = String(number)
            if ( r.length === 1 ) {
                r = '0' + r
            }
            return r
        }
        Date.prototype.toISOString = function() {
            return this.getUTCFullYear()
                + '-' + pad( this.getUTCMonth() + 1 )
                + '-' + pad( this.getUTCDate() )
                + 'T' + pad( this.getUTCHours() )
                + ':' + pad( this.getUTCMinutes() )
                + ':' + pad( this.getUTCSeconds() )
                + '.' + String( (this.getUTCMilliseconds()/1000).toFixed(3) ).slice( 2, 5 )
                + 'Z'
        }
    }())
}

if (!Date.now) {
    Date.now = function() { return new Date().getTime() }
}
