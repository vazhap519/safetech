import type { ServiceFaq } from "../model/types";

export default function FaqAccordion({ items }: { items: ServiceFaq[] }) {
    return (
        <div className="space-y-unit-md">
            {items.map((item, index) => (
                <details className="glass-card group overflow-hidden rounded-xl" key={item.question} open={index === 0}>
                    <summary className="flex cursor-pointer list-none items-center justify-between gap-4 p-unit-md font-headline-md text-lg text-white transition-colors marker:hidden hover:bg-white/5">
                        {item.question}
                        <svg aria-hidden="true" className="h-5 w-5 shrink-0 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                            <path d="m6 9 6 6 6-6" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                    </summary>
                    <div className="border-t border-white/5 p-unit-md text-on-surface-variant">
                        <p className="leading-relaxed">{item.answer}</p>
                    </div>
                </details>
            ))}
        </div>
    );
}
