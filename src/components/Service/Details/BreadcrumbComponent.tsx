import LocalizedLink from "@/components/ui/LocalizedLink";
import Icon from "@/components/ui/Icon";

export default function BreadcrumbComponent() {
    return (
        <>
            <LocalizedLink
                className="transition-colors hover:text-primary"
                href="/"
            >
                მთავარი
            </LocalizedLink>
            <Icon className="text-[14px]" name="chevron_right" />
        </>
    );
}
