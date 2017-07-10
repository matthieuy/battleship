let WS = (function(uri) {

    let Socket = function(uri) {
        this._uri = uri
        this._session = false
        this._listeners = {}
        this._subscribers = {}
        this._dataDefault = {}
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
                    return Flash.error("Error connecting Websocket!");
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

    Socket.prototype.on = function(type, listener) {
        if (typeof this._listeners[type] == "undefined") {
            this._listeners[type] = []
        }

        this._listeners[type].push(listener)

        return this;
    };

    Socket.prototype.fire = function(event) {
        if (typeof event == "string") {
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

    Socket.prototype.addDefaultData = function(name, value) {
        this._dataDefault[name] = value
        return this
    }

    Socket.prototype.callRPC = function(entryPoint, data, cb) {
        entryPoint = 'rpc/' + entryPoint
        if (!this._session) {
            return console.error('[RPC] Session unavailable');
        }

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
            if (typeof cb == 'function') {
                cb(obj)
            }
        }, function(error, desc) {
            console.log(error, desc);
        })
    }

    Socket.prototype.subscribe = function(topicName) {
        if (typeof this._subscribers[topicName] == 'undefined') {
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

    Socket.prototype.subscribeAction = function(topicName, dataName, cb) {
        this.subscribe(topicName)
        this.addAction(topicName, dataName, cb)

        return this
    }

    Socket.prototype.addAction = function(topicName, dataName, cb) {
        if (typeof this._subscribers[topicName][dataName] == 'undefined') {
            this._subscribers[topicName][dataName] = []
        }
        this._subscribers[topicName][dataName].push(cb)

        return this
    }

    return new Socket(uri)
})

module.exports = WS
