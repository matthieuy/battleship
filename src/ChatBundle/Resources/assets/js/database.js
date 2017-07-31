import Dexie from 'dexie'

const db = new Dexie('chat')

db.version(1).stores({
    messages: '++id, &id_message, game, timestamp, author_id, author_name, channel, recipient, text, *context',
})

db.open().catch(err => {
    console.error(`[DB] ${err.stack}`);
});

export default db;
