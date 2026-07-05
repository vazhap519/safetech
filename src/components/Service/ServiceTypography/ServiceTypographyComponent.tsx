import Typography from "@/components/ui/Typography";

export default function ServiceTypographyComponent() {
    return (

        <div className="flex flex-col md:flex-row justify-between items-end mb-unit-xl gap-unit-md">
            <div>
                <Typography as={"h2"} variant={"section-title"}>  სერვისების
                    კატალოგი</Typography>
              <Typography as={"p"} variant={"section-description"}>
                  შეარჩიეთ თქვენს
                  მოთხოვნებზე მორგებული გადაწყვეტილება
              </Typography>
            </div>
            <div className="font-mono-sm text-mono-sm text-on-surface-variant">Page 01 / 04</div>
        </div>
    )
}