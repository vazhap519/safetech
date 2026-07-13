import Typography from "@/components/ui/Typography";
import ShowCaseImageComponent from "@/components/Service/Details/ShowcaseComponent/ShowCaseImageComponent";

export default function ShowcaseComponent(){
    return (
        <div className="glass-card rounded-3xl overflow-hidden group">
          <ShowCaseImageComponent/>
            <div className="p-unit-lg space-y-unit-sm">
                <Typography as={"h4"} variant={"section-title"} className={"font-headline-md text-headline-md text-white"}>Enterprise LogCenter</Typography>
           <Typography as={"p"} variant={"description"} className={"font-body-md text-body-md text-on-surface-variant"}>250+ IP კამერის ინსტალაცია,
               Fiber Optic მაგისტრალები და ცენტრალიზებული სერვერული ოთახი.</Typography>

            </div>
        </div>

    )
}