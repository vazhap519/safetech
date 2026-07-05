import ProjectsSectionHeader from "@/components/Projects/ProjectsSectionHeader";
import StandardCard from "@/components/Projects/StandardCard";
import { projectStandards } from "@/lib/projects";

export default function StandardsSection() {
    return (
        <section className="overflow-hidden bg-background py-unit-xl" aria-labelledby="standards-title">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <div id="standards-title"><ProjectsSectionHeader centered title="ჩვენი სტანდარტები" /></div>
                <div className="grid grid-cols-1 gap-gutter md:grid-cols-3">
                    {projectStandards.map((standard) => <StandardCard key={standard.title} standard={standard} />)}
                </div>
            </div>
        </section>
    );
}
