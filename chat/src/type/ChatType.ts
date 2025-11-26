export type ChatType = {
    messages: Array<{
        to: {
            userId: number;
            nome: string
        },
        from: {
            userId: number;
            nome: string
        }
        message: string;
    }>
}