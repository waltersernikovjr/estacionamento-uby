import { ChatDao } from "../data/ChatDao.js";

export default class Chat {
    constructor(private readonly _chatDao: ChatDao) { }

    public async execute({ from, to, content }: Input) {
        const chat = await this._chatDao.get(from.userId);

        if (chat) {
            chat.messages.push({
                message: content,
                to,
                from,
            });

            await this._chatDao.save(from.userId, chat);

            return chat;
        } else {
            const newChat = {
                messages: [
                    {
                        message: content,
                        to,
                        from,
                    }
                ]
            };
            await this._chatDao.save(from.userId, newChat);

            return newChat;
        }

        return chat;
    }
}

type Input = {
    content: string,
    to: {
        userId: number;
        nome: string
    }, from: {
        userId: number;
        nome: string
    }
}