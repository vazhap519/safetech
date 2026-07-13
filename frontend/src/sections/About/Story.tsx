
 import StoryomponentsTypography from "../../components/About/StoryComponents/StoryComponentsTypography";
import StoryomponentsImage from "@/components/About/StoryComponents/StoryComponentsImage";

export default function StorySection() {
    return (
        <section className="py-unit-xl bg-surface-container-lowest">
            <div
                className="max-w-container-max mx-auto px-margin-desktop grid grid-cols-1 md:grid-cols-2 gap-unit-xl items-center">
                <StoryomponentsTypography/>
<StoryomponentsImage/>
            </div>
        </section>

    )
}