import Icon from "@/components/ui/Icon";

export default function Industriescomponent({
    icon,
    title,
}: {
    icon: string;
    title: string;
}) {
    return (
        <div className="glass-card group relative flex h-56 flex-col items-center justify-center gap-4 overflow-hidden rounded-lg px-4 text-center transition-all hover:bg-primary/10 md:h-60">
            <Icon
                className="text-[42px] transition-transform group-hover:scale-110"
                name={icon}
            />
            <span className="font-headline-md text-xl md:text-[22px]">{title}</span>
        </div>

    )
}
