<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $process = [
            ['title' => 'კონსულტაცია', 'description' => 'მოთხოვნებისა და ბიზნესის საჭიროებების დეტალური განხილვა.'],
            ['title' => 'აუდიტი', 'description' => 'ობიექტის, არსებული ინფრასტრუქტურისა და რისკების შეფასება.'],
            ['title' => 'პროექტირება', 'description' => 'ტექნიკური გეგმისა და გამჭვირვალე ხარჯთაღრიცხვის მომზადება.'],
            ['title' => 'დანერგვა', 'description' => 'მონტაჟი, კონფიგურაცია, ტესტირება და დოკუმენტირება.'],
            ['title' => 'მხარდაჭერა', 'description' => 'მონიტორინგი, SLA და ოპერატიული ტექნიკური მხარდაჭერა.'],
        ];

        $services = [
            ['slug'=>'cctv','name'=>'IP ვიდეოსამეთვალყურეო სისტემები','eyebrow'=>'Enterprise Security','icon'=>'videocam','title'=>'პროფესიონალური IP ვიდეოსამეთვალყურეო სისტემები ბიზნესისთვის','description'=>'4K კამერები, AI ვიდეოანალიტიკა და ცენტრალიზებული მონიტორინგი.','keywords'=>['CCTV','IP კამერები','კამერების მონტაჟი თბილისში'],'industries'=>['ოფისები','საწყობები','სასტუმროები','საწარმოები']],
            ['slug'=>'access-control','name'=>'დაშვების კონტროლი','eyebrow'=>'Access Management','icon'=>'badge','title'=>'ჭკვიანი დაშვების კონტროლი და სამუშაო დროის აღრიცხვა','description'=>'ბარათები, ბიომეტრია, ტურნიკეტები და ვიზიტორთა მართვა.','keywords'=>['დაშვების კონტროლი','ბიომეტრია','ტურნიკეტი'],'industries'=>['ოფისები','სასტუმროები','კლინიკები','საწყობები']],
            ['slug'=>'networking','name'=>'ქსელური ინფრასტრუქტურა','eyebrow'=>'Enterprise Networking','icon'=>'lan','title'=>'სწრაფი და დაცული ქსელური ინფრასტრუქტურა','description'=>'LAN, Wi-Fi, ოპტიკური ქსელი, Firewall და სტრუქტურირებული კაბელირება.','keywords'=>['ქსელის მოწყობა','Wi-Fi','LAN','Firewall'],'industries'=>['ოფისები','სასტუმროები','განათლება','რიტეილი']],
            ['slug'=>'server-infrastructure','name'=>'სერვერული ინფრასტრუქტურა','eyebrow'=>'Data Center','icon'=>'dns','title'=>'საიმედო სერვერული ინფრასტრუქტურა თქვენი ბიზნესისთვის','description'=>'სერვერები, Storage, ვირტუალიზაცია, Backup და უწყვეტი მუშაობა.','keywords'=>['სერვერები','ვირტუალიზაცია','Backup','Storage'],'industries'=>['ფინანსები','ლოგისტიკა','კლინიკები','საწარმოები']],
        ];

        foreach ($services as $index => $data) {
            $service = Service::query()->updateOrCreate(['slug'=>$data['slug']], $data + [
                'seo_description'=>$data['description'].' SafeTech-ის პროექტირება, მონტაჟი და მხარდაჭერა საქართველოში.',
                'hero_image'=>null,'highlights'=>['პროფესიონალური მონტაჟი','წერილობითი გარანტია','ტექნიკური მხარდაჭერა'],
                'overview'=>['title'=>'სრულად მართული გადაწყვეტა','paragraphs'=>[$data['description'],'სისტემა იგეგმება უსაფრთხოების, მასშტაბირებისა და ბიზნესის უწყვეტობის მოთხოვნების მიხედვით.'],'stats'=>[['value'=>'24/7','label'=>'მხარდაჭერა'],['value'=>'SLA','label'=>'რეაგირება']]],
                'benefits'=>[['icon'=>'verified_user','title'=>'საიმედოობა','description'=>'ტესტირებული არქიტექტურა და დოკუმენტირებული დანერგვა.'],['icon'=>'trending_up','title'=>'მასშტაბირება','description'=>'ინფრასტრუქტურა იზრდება ბიზნესთან ერთად.']],
                'solutions'=>[['icon'=>$data['icon'],'title'=>$data['name'],'description'=>$data['description'],'featured'=>true]],
                'process'=>$process,'brands'=>[],'warranty'=>'გარანტიის ზუსტი ვადა და პირობები ფიქსირდება კომერციულ შეთავაზებაში.','sla'=>'რეაგირების დრო განისაზღვრება არჩეული მხარდაჭერის პაკეტით.','is_published'=>true,'sort_order'=>$index,
            ]);

            foreach ([['რამდენ ხანს გრძელდება დანერგვა?','ვადა დამოკიდებულია ობიექტის ზომასა და სირთულეზე და ფიქსირდება პროექტის გეგმაში.'],['გთავაზობთ თუ არა გარანტიას?','დიახ, შესრულებულ სამუშაოსა და მოწყობილობებზე ვრცელდება წერილობითი გარანტია.']] as $order => [$question,$answer]) {
                Faq::query()->updateOrCreate(['service_id'=>$service->id,'question'=>$question],['answer'=>$answer,'context'=>'service','is_active'=>true,'sort_order'=>$order]);
            }
        }

        Project::query()->updateOrCreate(['slug'=>'global-logistics-hub'],[
            'name'=>'Global Logistics Hub','title'=>'ლოგისტიკური ჰაბის ერთიანი IT და უსაფრთხოების ინფრასტრუქტურა','description'=>'საწყობის ქსელის, ვიდეომონიტორინგისა და სერვერული კვანძის ცენტრალიზებული მართვა.','seo_description'=>'ლოგისტიკური ჰაბის CCTV, ქსელური და სერვერული ინფრასტრუქტურის სრული Case Study.','image'=>null,'image_alt'=>'ლოგისტიკური ჰაბის IT ინფრასტრუქტურა','category'=>'warehouses','technology'=>'Enterprise Infrastructure','icon'=>'warehouse','accent'=>'secondary',
            'meta'=>[['label'=>'სექტორი','value'=>'ლოგისტიკა'],['label'=>'მასშტაბი','value'=>'5 000 მ²']],
            'scope'=>[['value'=>'64','label'=>'IP კამერა'],['value'=>'10G','label'=>'Backbone']],
            'specs'=>[['value'=>'99.9%','label'=>'ხელმისაწვდომობა']],
            'challenges'=>[['icon'=>'warning','title'=>'ფართო ტერიტორია','description'=>'მაღალი დაფარვისა და ცენტრალიზებული კონტროლის საჭიროება.']],
            'solutions'=>[['icon'=>'hub','title'=>'ერთიანი არქიტექტურა','description'=>'სეგმენტირებული ქსელი, ცენტრალიზებული მონიტორინგი და დაცული სერვერები.','featured'=>true]],
            'process'=>[['title'=>'აუდიტი','description'=>'რისკებისა და მოთხოვნების შეფასება.'],['title'=>'დანერგვა','description'=>'ეტაპობრივი მონტაჟი ოპერაციების შეჩერების გარეშე.']],
            'gallery'=>[],'results'=>[['value'=>'40%','title'=>'სწრაფი რეაგირება','description'=>'ინციდენტების იდენტიფიკაციის დროის შემცირება.','accent'=>'secondary']],
            'testimonial'=>[],'related'=>[],'is_featured'=>true,'is_published'=>true,'sort_order'=>0,'published_at'=>now(),
        ]);

        foreach ([['გიორგი','მაისურაძე','აღმასრულებელი დირექტორი'],['ნინო','ბერიძე','ტექნიკური დირექტორი'],['ლევან','კაპანაძე','ქსელის არქიტექტორი'],['ანა','გელაშვილი','პროექტების მენეჯერი']] as $index => [$first,$last,$position]) {
            TeamMember::query()->updateOrCreate(['first_name'=>$first,'last_name'=>$last],['position'=>$position,'image'=>null,'socials'=>[],'is_active'=>true,'sort_order'=>$index]);
        }

        foreach ([
            'contact' => ['phone'=>'+995 32 2 00 00 00','email'=>'info@safetech.ge','address'=>'თბილისი, საქართველო','whatsapp'=>'995599123456','whatsapp_message'=>'გამარჯობა, მაინტერესებს SafeTech-ის მომსახურება.','hours'=>'ორშ - პარ: 10:00 - 19:00','lead_email'=>'safetechgeorgia@gmail.com'],
            'socials' => ['links'=>[
                ['network'=>'facebook','label'=>'Facebook','href'=>'https://www.facebook.com/'],
                ['network'=>'linkedin','label'=>'LinkedIn','href'=>'https://www.linkedin.com/'],
                ['network'=>'instagram','label'=>'Instagram','href'=>'https://www.instagram.com/'],
                ['network'=>'tiktok','label'=>'TikTok','href'=>'https://www.tiktok.com/'],
            ]],
            'seo' => ['site_name'=>'SafeTech','default_image'=>'/demo.png'],
            'branding' => ['site_name'=>'SafeTech','tagline'=>'თქვენი ბიზნესის ტექნოლოგიური და უსაფრთხოების გარანტი.','logo'=>null,'footer_logo'=>null,'favicon'=>null,'default_image'=>null],
        ] as $key => $value) SiteSetting::query()->updateOrCreate(['key'=>$key],['group'=>'general','value'=>$value,'is_public'=>true]);
    }
}
