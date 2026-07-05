import Typography from "@/components/ui/Typography";

export default function StoryomponentsTypography(){
return (
    <div className="space-y-unit-lg">
        <Typography as="h2"
                    variant="section-title">
            ინჟინერია და ხარისხი
        </Typography>

        <div className="w-20 h-1 bg-primary"></div>
        <Typography as="p" variant="section-description">     ჩვენი ისტორია დაიწყო რწმენით, რომ ნებისმიერი ციფრული ტრანსფორმაციის საფუძველი არის მყარი და
            უსაფრთხო ფიზიკური ინფრასტრუქტურა. SafeTech აერთიანებს მაღალი დონის საინჟინრო ექსპერტიზას და
            უახლეს ტექნოლოგიებს.</Typography>

        <Typography as="p" variant="section-description">
            ჩვენი მიდგომა ეფუძნება სისუფთავეს, სიზუსტეს და გრძელვადიან საიმედოობას. თითოეული კაბელი,
            თითოეული სენსორი და თითოეული სერვერი არის კარგად გააზრებული არქიტექტურის ნაწილი.
        </Typography>
    </div>

)
}
