import Typography from "@/components/ui/Typography";

export default function Header(){
    return (
        <Typography
            as="h1"
            variant="[slug]-hero"
        >
            პროფესიონალური <br />
            <span className="text-primary">
                IT და უსაფრთხოების
            </span>{" "}
            სერვისები
        </Typography>

    )
}