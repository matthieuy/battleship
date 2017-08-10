/**
 * Websocket manager
 * @type {Function}
 */
let WS = (function(uri) {
    /**
     * Socket object
     * @param uri
     * @constructor
     */
    let Socket = function(uri) {
        this._uri = uri
        this._session = false
        this._listeners = {}
        this._subscribers = {}
        this._dataDefault = {}
        this._tryRPC = 0

        /**
         * Dispatch received data
         * @param topicName
         * @param result
         * @private
         */
        this._onResult = function(topicName, result) {
            let that = this
            $.each(result, function(dataName, obj) {
                if (typeof that._subscribers[topicName][dataName] !== 'undefined') {
                    console.info('[Socket] Receive from '+topicName, '{'+ dataName+'}', obj)
                    let listCb = that._subscribers[topicName][dataName]
                    for (let i=0; i<listCb.length; i++) {
                        listCb[i](obj)
                    }
                }
            })
        }
    }

    /**
     * Connexion WS
     */
    Socket.prototype.connect = function () {
        let that = this

        // Topic subscribe
        that.on('socket/connect', function() {
            $.each(that._subscribers, function(topicName, actions) {
                that._session.subscribe(topicName, function(uri, result) {
                    that._onResult(topicName, result)
                })
            })
        })

        ab.connect(this._uri,
            function(session) {
                that._session = session
                that.fire({
                    type: "socket/connect",
                    data: session
                })
            },
            function(code, reason) {
                that._session = false
                console.error('[Socket] Error : ' + reason + ' (code:' + code + ')')
                if (code === 3) {
                    return Flash.error("error_ws")
                }
                that.fire({
                    type: "socket/disconnect",
                    data: {
                        code: code,
                        reason: reason
                    }
                })
            }
        )
    }

    /**
     * Add listener on event
     * @param type
     * @param listener
     * @returns {Socket}
     */
    Socket.prototype.on = function(type, listener) {
        if (typeof this._listeners[type] === "undefined") {
            this._listeners[type] = []
        }

        this._listeners[type].push(listener)

        return this
    };

    /**
     * Fire a event
     * @param event
     */
    Socket.prototype.fire = function(event) {
        if (typeof event === "string") {
            event = { type: event }
        }
        if (!event.target) {
            event.target = this
        }

        if (!event.type) {
            throw new Error("[Socket] Event object missing 'type' property.")
        }

        console.info('[Socket] event : ' + event.type)

        if (this._listeners[event.type] instanceof Array){
            let listeners = this._listeners[event.type]
            for (let i=0, len=listeners.length; i < len; i++){
                listeners[i].call(this, event.data)
            }
        }
    }

    /**
     * Remove listener
     * @param type
     * @param listener
     * @returns {Socket}
     */
    Socket.prototype.off = function(type, listener) {
        if (this._listeners[type] instanceof Array) {
            let listeners = this._listeners[type]
            for (let i=0, len=listeners.length; i<len; i++) {
                if (listeners[i] === listener){
                    listeners.splice(i, 1)
                    break
                }
            }
        }

        return this
    }

    /**
     * Add data to send on all RPC calls
     * @param name
     * @param value
     * @returns {Socket}
     */
    Socket.prototype.addDefaultData = function(name, value) {
        this._dataDefault[name] = value
        return this
    }

    /**
     * Call RPC
     * @param entryPoint
     * @param data
     * @param cb
     * @returns {number}
     */
    Socket.prototype.callRPC = function(entryPoint, data, cb) {
        if (!this._session) {
            this._tryRPC++
            console.error('[RPC] Session unavailable (try ' + this._tryRPC + ')')
            if (this._tryRPC >= 15) {
                let message = '[RPC] Session unavailable after ' + this._tryRPC + 'try'
                console.error(message)
                return Flash.error(message)
            } else {
                let self = this
                return setTimeout(function() { self.callRPC(entryPoint, data, cb) }, 1000)
            }
        }

        entryPoint = 'rpc/' + entryPoint

        if (typeof data !== 'undefined') {
            $.extend(data, this._dataDefault);
        } else {
            data = this._dataDefault;
        }

        console.info('[RPC] Call '+entryPoint, data)

        this._session.call(entryPoint, data).then(function(obj) {
            console.info('[RPC] Receive from ' + entryPoint, obj)
            if (obj.error) {
                return Flash.error(obj.error)
            }
            if (typeof cb === 'function') {
                cb(obj)
            }
        }, function(error, desc) {
            console.log(error, desc)
        })
    }

    /**
     * Subscribe to a topic
     * @param topicName
     * @returns {Socket}
     */
    Socket.prototype.subscribe = function(topicName) {
        if (typeof this._subscribers[topicName] === 'undefined') {
            console.info('[Socket] Subscribe topic', topicName)
            this._subscribers[topicName] = {}
            if (this._session) {
                let that = this
                this._session.subscribe(topicName, function(uri, result) {
                    that._onResult(topicName, result)
                })
            }
        }
        return this
    }

    /**
     * Unsubscribe to topic
     * @param topicName
     * @returns {Socket}
     */
    Socket.prototype.unsubscribe = function(topicName) {
        if (typeof this._subscribers[topicName] !== 'undefined') {
            delete this._subscribers[topicName]
            if (this._session && this._session._subscriptions[topicName]) {
                console.info('[Socket] Unsubscribe topic', topicName)
                this._session.unsubscribe(topicName)
            }
        }

        return this
    }

    /**
     * Subscribe with callback action
     * @param topicName
     * @param dataName
     * @param cb
     * @returns {Socket}
     */
    Socket.prototype.subscribeAction = function(topicName, dataName, cb) {
        this.subscribe(topicName)
        this.addAction(topicName, dataName, cb)

        return this
    }

    /**
     * Add callback action
     * @param topicName
     * @param dataName
     * @param cb
     * @returns {Socket}
     */
    Socket.prototype.addAction = function(topicName, dataName, cb) {
        if (typeof this._subscribers[topicName][dataName] === 'undefined') {
            this._subscribers[topicName][dataName] = []
        }
        this._subscribers[topicName][dataName].push(cb)

        return this
    }

    return new Socket(uri)
})

module.exports = WS
