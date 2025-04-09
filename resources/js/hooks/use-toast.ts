import { useState, useCallback } from 'react';

interface ToastOptions {
    title: string;
    description?: string;
    duration?: number;
}

interface Toast extends ToastOptions {
    id: number;
}

export const useToast = () => {
    const [toasts, setToasts] = useState<Toast[]>([]);

    const toast = useCallback(
        ({ title, description, duration = 3000 }: ToastOptions) => {
            const id = Date.now();
            const newToast = { id, title, description };

            setToasts((prev) => [...prev, newToast]);

            setTimeout(() => {
                setToasts((prev) => prev.filter((t) => t.id !== id));
            }, duration);
        },
        []
    );

    return { toast, toasts };
}; 