import Typography from "@/components/ui/Typography";

export default function Infrastructurecomponent() {
    return (
        <div className="flex items-start gap-4">
            <span className="material-symbols-outlined text-primary mt-1">check_circle</span>
            <div>
                <Typography className="font-headline-md text-headline-md text-on-surface" as={"h3"} variant={'section-title'}>მასშტაბირებადი არქიტექტურა</Typography>
                <Typography className="text-on-surface-variant font-body-md" as={"p"} variant={'description'}>სისტემა, რომელიც მარტივად იზრდება თქვენს ბიზნესთან ერთად.</Typography>
            </div>
        </div>
    )
}
