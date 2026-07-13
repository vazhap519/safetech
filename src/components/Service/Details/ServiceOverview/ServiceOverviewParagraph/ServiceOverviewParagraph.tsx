import Typography from "@/components/ui/Typography";

export default function ServiceOverviewParagraph(){
    return (
        <div className="space-y-unit-md font-body-md text-body-md text-on-surface-variant leading-relaxed">
            <Typography as={"p"} variant={"description"}>SafeTech გთავაზობთ IP ვიდეოსამეთვალყურეო სისტემების სრულ ციკლს, დაწყებული პროექტირებიდან,
                დამთავრებული ექსპლუატაციაში მიღებით. ჩვენი გადაწყვეტილებები ეფუძნება თანამედროვე ქსელურ
                ინფრასტრუქტურას, რაც უზრუნველყოფს მაღალი ხარისხის ვიდეო ნაკადების სტაბილურ გადაცემას.</Typography>
            <Typography as={"p"} variant={"description"}>ჩვენი სისტემები მარტივად მასშტაბირებადია, რაც საშუალებას გაძლევთ ნებისმიერ დროს დაამატოთ
                ახალი კამერები ან ლოკაციები ცენტრალიზებულ ქსელში. უსაფრთხოება ჩვენი პრიორიტეტია, ამიტომ
                ვიყენებთ მონაცემთა შიფრაციის უმაღლეს სტანდარტებს.</Typography>
        </div>

    )
}