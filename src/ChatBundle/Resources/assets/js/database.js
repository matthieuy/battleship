import Dexie from 'dexie'

const db = new Dexie('chat')

// Version 1 : Initial version
db.version(1).stores({
    messages: '++id, &id_message, game, timestamp, author_id, author_name, tab, text, *context, unread',
})

// Version 2 : add user_id
db.version(2).stores({
    messages: '++id, &id_message, user_id, game, timestamp, author_id, author_name, tab, text, *context, unread',
})

db.open().catch(err => {
    console.error(`[DB] ${err.stack}`);
});

export default db;
