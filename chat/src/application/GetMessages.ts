import { ChatDao } from "../data/ChatDao.js";

export default class GetMessages {
    constructor(private readonly _chatDao: ChatDao) { }

    public async execute(userId: number) {
        return this._chatDao.get(userId);
    }
}