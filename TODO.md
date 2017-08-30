TODO
====

- [x] Do a first alpha test :
  - Bugs reported :
    - [X] Chat :
      - [X] Mark message read on open chat
      - [X] Private message : show the send message
    - [X] Notifications :
      - [X] Discord webhook : message global insteal personal
      - [X] Discord bot : don't work
    - [ ] Game :
      - [X] Boats / explose images : error (same as avatar)
      - [X] Change lap don't work if multi player (no AI)
      - [X] Boat image hide if explose on it
      - [X] Player position == 0 can't be shoot with bonus
      - [ ] Simulate shoot : status don't synchronize
      - [ ] Score : don't be synchronize (WS) but DB value is great 
    - [X] Bonus :
      - [X] Robber : send chat message with user instead system
      - [X] Skip player : don't work correctly if after "next lap" event
    - [X] Other :
      - [X] Websocket : Error "Mysql server has gone away"
      - [X] Flash close button don't work
      - [X] Weapon : switch button select and rotate
      - [X] Stats : division by zero (StatsManager line 71)
  - Features :
    - [X] Chat :
        - [X] Button to purge the local indexedDB (and localStorage)
    - [ ] ISO view
    - [ ] Add screenshot or gif in documentation
    - [ ] Bonus :
        - [ ] Radar
        - [ ] Decrement penalty time

