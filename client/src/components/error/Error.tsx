import React from "react";

export const ErrorAlert: React.FC<{ message: string; onClose: () => void }> = ({ message, onClose }) => {
    return (
        <div className="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full">
            <div className="bg-red-600 text-white px-6 py-4 rounded-lg shadow-2xl flex items-center justify-between">
                <span className="text-sm font-medium">{message}</span>
                <button
                    onClick={onClose}
                    className="ml-4 text-white hover:bg-red-700 rounded-full p-1 transition"
                >
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    );
};