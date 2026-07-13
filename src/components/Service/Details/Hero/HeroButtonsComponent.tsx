import Button from "@/components/ui/Button";

export default function HeroButtonsComponent() {
    return (
        <div className="flex flex-wrap gap-unit-md pt-unit-md">
            <Button variant={"hero-primary"}
                className="bg-primary-container text-on-primary-container px-unit-xl py-unit-md rounded-xl font-headline-md text-headline-md glow-button">უფასო
                კონსულტაცია
            </Button>
            <Button variant={"hero-secondary"}
                className="border border-outline-variant bg-white/5 backdrop-blur hover:bg-white/10 text-white px-unit-xl py-unit-md rounded-xl font-headline-md text-headline-md transition-all">ფასის
                მიღება
            </Button>
        </div>

    )
}