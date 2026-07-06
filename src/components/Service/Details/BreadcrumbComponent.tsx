import Link from "next/link";
import Icon from "@/components/ui/Icon";

export default function BreadcrumbComponent(){
    return (
        <>
            <Link className="hover:text-primary transition-colors" href="#">მთავარი</Link>
            <Icon className="text-[14px]" name="chevron_right" />
        </>
    )
}
