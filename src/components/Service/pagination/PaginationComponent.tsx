import Button from "@/components/ui/Button";
import Icon from "@/components/ui/Icon";

export default function PaginationComponent() {
    return (
        <div className="flex justify-center items-center gap-unit-md">
            <Button variant={"primary"}
                className="w-10 h-10 rounded-lg glass-card flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors">
                <Icon name="chevron_left" />
            </Button>
            <button
                className="w-10 h-10 rounded-lg bg-tertiary-container text-white flex items-center justify-center font-bold">1
            </button>


            <Button variant={"primary"}
                className="w-10 h-10 rounded-lg glass-card flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors">
                <Icon name="chevron_right" />
            </Button>
        </div>
    )
}
