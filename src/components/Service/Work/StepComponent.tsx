import StepTypographyComponent from "@/components/Service/Work/StepTypographyComponent";

type StepComponentProps = {
    description: string;
    index: number;
    title: string;
};

export default function StepComponent({
    description,
    index,
    title,
}: StepComponentProps){
    return (
        <div className="flex flex-col items-center text-center">


            <StepTypographyComponent
                description={description}
                index={index}
                title={title}
            />

  </div>
    )
}
