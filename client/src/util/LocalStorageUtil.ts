export default class LocalStorageUtil {
    static set(key: string, value: any) {
        localStorage.setItem(key, JSON.stringify(value));
        window.dispatchEvent(new CustomEvent("localstorage-update", { detail: { key, value } }));
    };

    static get<T>(key: string): T | null {
        const raw = localStorage.getItem(key);
        if (!raw) return null;
        try {
            return JSON.parse(raw) as T;
        } catch {
            return null;
        }
    }
}