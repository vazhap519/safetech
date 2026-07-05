import Link from "next/link";

export default function BreadcrumbComponent(){
    return (
        <>
            <Link className="hover:text-primary transition-colors" href="#">მთავარი</Link>
            <span className="material-symbols-outlined text-[14px]">chevron_right</span>
        </>
    )
}