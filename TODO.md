TODO
====

- [x] Do a first alpha test :
  - Bugs reported :
    - [ ] Chat :
      - [ ] Mark message read on open chat
      - [ ] Private message : show the send message
    - [ ] Notifications :
      - [ ] Discord webhook : message global insteal personal
      - [ ] Discord bot : don't work
    - [ ] Game :
      - [X] Boats / explose images : error (same as avatar)
      - [ ] Change lap don't work if multi player (no AI)
      - [ ] Boat image hide if explose on it
      - [ ] Player position == 0 can't be shoot with bonus
      - [ ] Simulate shoot : status don't synchronize
      - [ ] Score : don't be synchronize (WS) but DB value is great 
    - [ ] Bonus :
      - [ ] Robber : send chat message with user instead system
      - [ ] Skip player : don't work correctly if after "next lap" event
    - [ ] Other :
      - [X] Websocket : Error "Mysql server has gone away"
      - [X] Flash close button don't work
      - [x] Weapon : switch button select and rotate
      - [ ] Stats : division by zero (StatsManager line 71)
  - Features :
    - [ ] Chat :
        - [ ] Button to purge the local indexedDB (and localStorage)
    - [ ] ISO view
    - [ ] Add screenshot or gif in documentation
    - [ ] Bonus :
        - [ ] Radar
        - [ ] Decrement penalty time

