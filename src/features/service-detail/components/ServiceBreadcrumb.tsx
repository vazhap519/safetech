import Link from "next/link";
import Icon from "@/components/ui/Icon";

export default function ServiceBreadcrumb({ current }: { current: string }) {
    return (
        <nav aria-label="Breadcrumb" className="mb-unit-lg text-label-md text-on-surface-variant">
            <ol className="flex flex-wrap items-center gap-unit-sm">
                <li><Link className="transition-colors hover:text-primary" href="/">მთავარი</Link></li>
                <li aria-hidden="true">
                    <Icon className="text-[14px]" name="chevron_right" />
                </li>
                <li><Link className="transition-colors hover:text-primary" href="/services">სერვისები</Link></li>
                <li aria-hidden="true">
                    <Icon className="text-[14px]" name="chevron_right" />
                </li>
                <li aria-current="page" className="text-primary">{current}</li>
            </ol>
        </nav>
    );
}
