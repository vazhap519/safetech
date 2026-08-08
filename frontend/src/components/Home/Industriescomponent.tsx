import Icon from "@/components/ui/Icon";

export default function Industriescomponent({
    icon,
    title,
}: {
    icon: string;
    title: string;
}) {
    return (
        <div className="glass-card group relative flex min-h-48 min-w-0 flex-col items-center justify-center gap-4 overflow-hidden rounded-lg px-5 py-6 text-center transition-all hover:bg-primary/10 sm:min-h-52 md:min-h-60">
            <Icon
                className="shrink-0 text-[38px] transition-transform group-hover:scale-110 sm:text-[42px]"
                name={icon}
            />
            <span className="max-w-full break-words font-headline-md text-lg leading-snug [overflow-wrap:anywhere] sm:text-xl md:text-[22px]">
                {title}
            </span>
        </div>
    );
}
