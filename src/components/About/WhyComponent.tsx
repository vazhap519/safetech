import Typography from "@/components/ui/Typography";

export default function WhyComponent() {
    return (
        <div className="glass-card p-unit-lg rounded-2xl text-center">
            <span className="material-symbols-outlined text-primary text-3xl">architecture</span>
            <div>
                <Typography
                    as="h3"
                    variant="why-component-header"
                >პროფესიონალური დაგეგმვა</Typography>
                <Typography
                    as="p"
                    variant="description"
                >ჩვენ არ ვიწყებთ მუშაობას დეტალური ნახაზებისა და
                    სქემების გარეშე.</Typography>
            </div>
        </div>

    )
}