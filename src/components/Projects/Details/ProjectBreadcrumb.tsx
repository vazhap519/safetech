import Link from "next/link";

export default function ProjectBreadcrumb({ current }: { current: string }) {
    return (
        <nav aria-label="Breadcrumb" className="font-mono-sm text-mono-sm text-on-surface-variant">
            <ol className="flex flex-wrap items-center gap-2">
                <li><Link className="transition-colors hover:text-primary" href="/">მთავარი</Link></li>
                <li aria-hidden="true">›</li>
                <li><Link className="transition-colors hover:text-primary" href="/projects">პროექტები</Link></li>
                <li aria-hidden="true">›</li>
                <li aria-current="page" className="text-primary-fixed-dim">{current}</li>
            </ol>
        </nav>
    );
}
