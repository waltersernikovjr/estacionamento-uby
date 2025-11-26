import { ChatType } from "../type/ChatType.js";

export interface ChatDao {
    get(userId: number): Promise<ChatType | undefined>;
    save(userId: number, chat: ChatType): Promise<void>;
}

export class InmemoryChatDao implements ChatDao {
    constructor(private readonly _data: Map<number, ChatType> = new Map()) { }

    public async get(userId: number): Promise<ChatType | undefined> {
        return this._data.get(userId);
    }

    public async save(userId: number, chat: ChatType): Promise<void> {
        this._data.set(userId, chat);
    }
}