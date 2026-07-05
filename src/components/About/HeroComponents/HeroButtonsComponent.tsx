import ActionLink from "@/components/ui/ActionLink";

export default function HeroButtons() {
    return (
        <div className="flex flex-col items-center justify-center gap-unit-md pt-4 sm:flex-row">
            <ActionLink href="/contact">მოთხოვნა</ActionLink>
            <ActionLink href="/projects" variant="glass">ჩვენი პროექტები</ActionLink>
        </div>
    );
}
