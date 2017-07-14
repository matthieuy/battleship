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

    // Delete game link
    $('#link-delete-game').click(function() {
        return window.confirm('Are you sure to delete the game?');
    });
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

/**
 * Detect if device is mobile
 * @returns {boolean}
 */
window.isMobile = function() {
    return /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(navigator.userAgent)
}

// Warning when offline
if (typeof navigator.onLine !== 'undefined') {
    let addOfflineWarn = () => { $('body').prepend('<div id="offline"><div id="offline-msg"><i class="gi gi-aerial-signal"></i> Your device is offline !</div><div id="offline-disable"></div></div>') }
    $(window).on('offline', addOfflineWarn)
    $(window).on('online', function() { $('#offline').remove() })
    if (!navigator.onLine) {
        addOfflineWarn()
    }
}
