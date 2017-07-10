#SSL Configuration

- Change the variable `use_ssl` to `true` in `app/config/parameters.yml`

- Install [Stunnel](https://www.stunnel.org/index.html) : 

    ```cmd
     sudo apt-get install stunnel
     ```

- Update configuration such as following : 
    
    ```cmd
    vi /etc/stunnel/battleship.conf
    ```

    ```ini
    # Certificate (You can use let's encrypt)
    cert = /my/way/to/ssl.crt
    key = /my/way/to/not_crypted.key
    
    chroot = /var/run/stunnel4/
    pid = /stunnel.pid
    
    # User id
    setuid = nobody
    
    # Group id
    #setgid = nobody
    
    [websockets]
    # Socket client side
    accept = 8443
    # Websocket server
    connect = 127.0.0.1:8080
    ```

    The `accept` parameter is the same as your `parameters.yml` file
    
    The `connect` parameter is the websocket adress 

- Save the file and start stunnel : 

    ```cmd
    /etc/init.d/stunnel4 start
    ```

- Launch your websocket server with the `connect` IP and port :

    ```cmd
    php bin/console gos:websocket:server -a 127.0.0.1 -p 8080 --env=prod
    ```
