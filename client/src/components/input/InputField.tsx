export const InputField = ({
    label,
    type = "text",
    placeholder,
    maxLength,
    className = "",
}: {
    label: string;
    type?: string;
    placeholder: string;
    maxLength?: number;
    className?: string;
}) => {
    return (
        <div className="flex flex-col">
            <label className="text-sm font-medium text-gray-300 m-2">{label}</label>
            <input
                type={type}
                placeholder={placeholder}
                maxLength={maxLength}
                className={`bg-gray-800 text-white h-12 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition ${className}`}
            />
        </div>
    );
};