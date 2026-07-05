import Typography from "@/components/ui/Typography";

export default function HowComponent() {
    return(
        <div className="flex flex-col md:flex-row items-center gap-unit-md group">
            <div className="md:w-1/2 md:text-right">
                <Typography
                    as="h3"
                    className="
        text-primary
        font-bold
        text-lg
    "
                >
                    კონსულტაცია
                </Typography>
                <Typography as="p" variant="section-description" >მოთხოვნების დეტალური შესწავლა</Typography>
            </div>
            <div
                className="w-4 h-4 rounded-full bg-primary z-10 shadow-[0_0_15px_rgba(37,99,235,0.8)]"></div>
            <div className="md:w-1/2"></div>
        </div>
    )
}