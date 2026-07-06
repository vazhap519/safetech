"use client";

import { useState } from "react";
import Icon from "@/components/ui/Icon";

export default function FaqComponent() {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <div className="glass-card rounded-xl overflow-hidden">
            <button
                className="w-full flex justify-between items-center p-unit-md text-left transition-colors hover:bg-white/5"
                onClick={() => setIsOpen(!isOpen)}
            >
                <span className="font-headline-md text-headline-md text-white">
                    რა დრო სჭირდება სისტემის ინსტალაციას?
                </span>

                <Icon
                    className={`
                        transition-transform
                        duration-300
                        ${isOpen ? "rotate-180" : ""}
                    `}
                    name="expand_more"
                />
            </button>

            <div
                className={`
                    overflow-hidden
                    transition-all
                    duration-300
                    ${
                    isOpen
                        ? "max-h-40 p-unit-md pt-0 border-t border-white/5"
                        : "max-h-0"
                }
                `}
            >
                <p className="text-on-surface-variant font-body-md">
                    ინსტალაციის დრო დამოკიდებულია ობიექტის სირთულეზე.
                    საშუალოდ, მცირე ოფისისთვის საჭიროა 2-3 დღე,
                    ხოლო მსხვილი საწარმოებისთვის - 1-2 კვირა.
                </p>
            </div>
        </div>
    );
}
