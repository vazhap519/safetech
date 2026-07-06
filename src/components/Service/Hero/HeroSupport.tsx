import Icon from "@/components/ui/Icon";

export default function HeroSupport() {
    return (
        <div
            className="flex items-center gap-2 bg-surface-container-low px-4 py-2 rounded-xl border border-outline-variant/10">
            <Icon className="text-[24px] text-secondary" name="support_agent" />
            <span className="font-label-md text-label-md">24/7 მხარდაჭერა</span>
        </div>
    )
}
