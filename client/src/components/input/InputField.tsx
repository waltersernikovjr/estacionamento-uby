import { forwardRef, type InputHTMLAttributes } from "react";

interface InputFieldProps extends InputHTMLAttributes<HTMLInputElement> {
    label: string;
    error?: string;
    as?: React.ElementType;
}

export const InputField = forwardRef<HTMLInputElement, InputFieldProps>(
    ({ label, error, as: Component = "input", className = "", ...props }, ref) => {
        return (
            <div className="flex flex-col gap-1">
                <label className="text-sm font-medium text-gray-700">
                    {label}
                    {props.required && <span className="text-red-500 ml-1">*</span>}
                </label>

                <Component
                    {...props}
                    ref={ref}
                    className={`
            px-4 py-3 rounded-lg border transition focus:outline-none focus:ring-2
            disabled:bg-gray-100 disabled:cursor-not-allowed
            ${error
                            ? "border-red-500 focus:ring-red-500"
                            : "border-gray-300 focus:border-gray-800 focus:ring-gray-800"
                        }
            ${className}
          `}
                />

                {error && <p className="text-red-500 text-xs mt-1">{error}</p>}
            </div>
        );
    }
);

InputField.displayName = "InputField";