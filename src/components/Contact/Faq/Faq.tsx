import Icon from "@/components/ui/Icon";

type FaqProps = {
    question: string;
    answer: string;
    defaultOpen?: boolean;
};

export default function Faq({
                                question,
                                answer,
                                defaultOpen = false,
                            }: FaqProps) {
    return (
        <details
            className="
                group
                glass-card
                rounded-xl
                overflow-hidden
                border
                border-outline-variant/20
                transition-all
                duration-300
            "
            open={defaultOpen}
        >
            <summary
                className="
                    flex
                    items-center
                    justify-between
                    gap-4
                    p-5
                    md:p-6
                    cursor-pointer
                    list-none
                    hover:bg-surface-container/30
                    transition-colors
                "
            >
                <span
                    className="
                        text-base
                        md:text-lg
                        font-semibold
                        text-on-surface
                    "
                >
                    {question}
                </span>

                <Icon
                    aria-hidden="true"
                    className="
                        shrink-0
                        text-primary
                        transition-transform
                        duration-300
                        group-open:rotate-180
                    "
                    name="expand_more"
                />
            </summary>

            <div
                className="
                    border-t
                    border-outline-variant/10
                    px-5
                    md:px-6
                    pb-5
                    md:pb-6
                    pt-4
                    text-on-surface-variant
                    leading-relaxed
                "
            >
                {answer}
            </div>
        </details>
    );
}
