import Typography from "@/components/ui/Typography";

export default function WhoComponent() {
    return (
        <div className="glass-card p-unit-lg rounded-xl flex flex-col gap-unit-md">
            <span className="material-symbols-outlined text-primary text-4xl">security</span>
            <Typography
                as="h3"
                className="
        text-xl
        font-headline-md
    "
            >Security Infrastructure</Typography>
            <Typography as="p" variant="section-description">  კომპლექსური უსაფრთხოების სისტემები, ინტელექტუალური
                მონიტორინგი და დაცვა.</Typography>
        </div>
    )
}