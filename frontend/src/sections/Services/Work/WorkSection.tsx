import StepComponent from "@/components/Service/Work/StepComponent";
import WorkTypographyComponent from "@/components/Service/Work/WorkTypographyComponent";

export default function WorkSection() {
    return (
        <section className="py-unit-xl bg-surface-container-low/30 relative overflow-hidden">
            <div className="ambient-glow -bottom-40 -right-40 opacity-30"></div>
            <div className="px-margin-desktop max-w-container-max mx-auto">
<WorkTypographyComponent/>
                <div className="relative">

                    <div className="hidden lg:block absolute top-12 left-0 w-full h-1 timeline-line opacity-20"></div>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-unit-lg relative">

                  <StepComponent/>
                  <StepComponent/>
                  <StepComponent/>
                  <StepComponent/>
                  <StepComponent/>
                  <StepComponent/>

                    </div>
                </div>
            </div>
        </section>
    )
}
