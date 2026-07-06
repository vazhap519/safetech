import Icon from "@/components/ui/Icon";

export default function Industriescomponent({
    icon,
    title,
}: {
    icon: string;
    title: string;
}) {
    return (
        <div className="group relative overflow-hidden rounded-3xl h-64 glass-card flex flex-col items-center justify-center gap-4 hover:bg-primary/10 transition-all cursor-pointer">
            <Icon
                className="text-5xl transition-transform group-hover:scale-110"
                name={icon}
            />
            <span className="font-headline-md text-headline-md">{title}</span>
        </div>

    )
}
