import {
    type InputHTMLAttributes,
    type SelectHTMLAttributes,
    type TextareaHTMLAttributes,
} from "react";

import clsx from "clsx";

type InputProps = {
    label: string;
    variant?: "default" | "floating";
    className?: string;
} & InputHTMLAttributes<HTMLInputElement>;

export function Input({
    label,
    variant = "floating",
    className,
    id,
    ...props
}: InputProps) {
    return (
        <div className="relative">
            <input
                id={id}
                placeholder=" "
                className={clsx(
                    `
                    peer
                    block
                    w-full
                    rounded-lg
                    border
                    border-outline-variant/30
                    bg-surface-container-low
                    text-on-surface
                    transition-all
                    duration-300
                    outline-none
                    focus:border-primary
                    focus:ring-0
                    `,
                    variant === "floating" &&
                        `
                        px-4
                        pt-6
                        pb-2
                        `,
                    className,
                )}
                {...props}
            />

            <label
                htmlFor={id}
                className="
                    absolute
                    left-4
                    top-4
                    text-on-surface-variant
                    font-label-md
                    text-label-md
                    transition-all
                    duration-300

                    peer-placeholder-shown:top-4
                    peer-placeholder-shown:text-body-md

                    peer-focus:top-1
                    peer-focus:text-primary
                    peer-focus:text-label-md
                "
            >
                {label}
            </label>
        </div>
    );
}

type TextareaProps = {
    label: string;
    className?: string;
} & TextareaHTMLAttributes<HTMLTextAreaElement>;

export function Textarea({
    label,
    className,
    id,
    ...props
}: TextareaProps) {
    return (
        <div className="relative">
            <textarea
                id={id}
                placeholder=" "
                className={clsx(
                    `
                    peer
                    block
                    w-full
                    rounded-lg
                    border
                    border-outline-variant/30
                    bg-surface-container-low
                    px-4
                    pt-6
                    pb-2
                    text-on-surface
                    transition-all
                    duration-300
                    outline-none
                    resize-none
                    focus:border-primary
                    focus:ring-0
                    `,
                    className,
                )}
                {...props}
            />

            <label
                htmlFor={id}
                className="
                    absolute
                    left-4
                    top-4
                    text-on-surface-variant
                    font-label-md
                    text-label-md
                    transition-all
                    duration-300

                    peer-placeholder-shown:top-4
                    peer-placeholder-shown:text-body-md

                    peer-focus:top-1
                    peer-focus:text-primary
                    peer-focus:text-label-md
                "
            >
                {label}
            </label>
        </div>
    );
}

type SelectOption =
    | string
    | {
          label: string;
          value: string;
      };

type SelectProps = {
    label: string;
    options: SelectOption[];
    placeholder?: string;
    className?: string;
} & SelectHTMLAttributes<HTMLSelectElement>;

export function Select({
    label,
    options,
    placeholder,
    className,
    id,
    ...props
}: SelectProps) {
    const normalizedOptions = options.map((option) =>
        typeof option === "string"
            ? { label: option, value: option }
            : option,
    );

    return (
        <div className="space-y-unit-xs">
            <label
                htmlFor={id}
                className="
                    text-label-md
                    font-label-md
                    text-on-surface-variant
                "
            >
                {label}
            </label>

            <select
                id={id}
                className={clsx(
                    `
                    w-full
                    rounded-lg
                    border
                    border-outline-variant/30
                    bg-surface-container-low
                    px-4
                    py-3
                    text-on-surface
                    appearance-none
                    outline-none
                    transition-all
                    duration-300
                    focus:border-primary
                    focus:ring-0
                    `,
                    className,
                )}
                {...props}
            >
                {placeholder ? <option value="">{placeholder}</option> : null}
                {normalizedOptions.map((option) => (
                    <option key={`${option.value}-${option.label}`} value={option.value}>
                        {option.label}
                    </option>
                ))}
            </select>
        </div>
    );
}
