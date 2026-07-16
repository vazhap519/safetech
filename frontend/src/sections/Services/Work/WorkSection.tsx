import StepComponent from "@/components/Service/Work/StepComponent";
import WorkTypographyComponent from "@/components/Service/Work/WorkTypographyComponent";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const defaultSteps = [
    {
        title: {
            ka: "კონსულტაცია",
            en: "Consultation",
            ru: "Консультация",
        },
        description: {
            ka: "საჭიროებების განსაზღვრა",
            en: "Define requirements",
            ru: "Определяем потребности",
        },
    },
    {
        title: {
            ka: "ობიექტის შეფასება",
            en: "Site assessment",
            ru: "Оценка объекта",
        },
        description: {
            ka: "ვათვალიერებთ სივრცეს და ტექნიკურ პირობებს.",
            en: "We inspect the space and technical conditions.",
            ru: "Изучаем пространство и технические условия.",
        },
    },
    {
        title: {
            ka: "პროექტირება",
            en: "Planning",
            ru: "Проектирование",
        },
        description: {
            ka: "ვამზადებთ გადაწყვეტას, აღჭურვილობას და ვადებს.",
            en: "We prepare the solution, equipment, and timeline.",
            ru: "Готовим решение, оборудование и сроки.",
        },
    },
    {
        title: {
            ka: "მონტაჟი",
            en: "Installation",
            ru: "Монтаж",
        },
        description: {
            ka: "ვასრულებთ პროფესიონალურ ინსტალაციას.",
            en: "We complete the professional installation.",
            ru: "Выполняем профессиональную установку.",
        },
    },
    {
        title: {
            ka: "გამართვა",
            en: "Configuration",
            ru: "Настройка",
        },
        description: {
            ka: "ვაკონფიგურირებთ სისტემას და ვამოწმებთ მუშაობას.",
            en: "We configure the system and test performance.",
            ru: "Настраиваем систему и проверяем работу.",
        },
    },
    {
        title: {
            ka: "მხარდაჭერა",
            en: "Support",
            ru: "Поддержка",
        },
        description: {
            ka: "გთავაზობთ მომსახურებას გაშვების შემდეგაც.",
            en: "We provide support after launch as well.",
            ru: "Поддерживаем систему после запуска.",
        },
    },
];

export default async function WorkSection() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(translations, "services.work.title", locale, {
        ka: "მუშაობის პროცესი",
        en: "Work process",
        ru: "Процесс работы",
    });

    return (
        <section className="py-unit-xl bg-surface-container-low/30 relative overflow-hidden">
            <div className="ambient-glow -bottom-40 -right-40 opacity-30"></div>
            <div className="px-margin-desktop max-w-container-max mx-auto">
<WorkTypographyComponent title={title}/>
                <div className="relative">

                    <div className="hidden lg:block absolute top-12 left-0 w-full h-1 timeline-line opacity-20"></div>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-unit-lg relative">

                  {defaultSteps.map((step, index) => (
                      <StepComponent
                          description={translateText(
                              translations,
                              `services.work.step.${index}.description`,
                              locale,
                              step.description,
                          )}
                          index={index + 1}
                          key={`work-step-${index}`}
                          title={translateText(
                              translations,
                              `services.work.step.${index}.title`,
                              locale,
                              step.title,
                          )}
                      />
                  ))}

                    </div>
                </div>
            </div>
        </section>
    )
}
