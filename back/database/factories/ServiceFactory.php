<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceFactory extends Factory
{
public function definition(): array
{
$title = $this->faker->unique()->sentence(3);

return [
'title' => $title,
'slug' => Str::slug($title),

// ✅ ONLY USED FIELDS
'short_description' => $this->faker->sentence(12),
'long_description' => $this->faker->paragraphs(4, true),

'phone' => '+9955' . $this->faker->numberBetween(10000000, 99999999),

'button_text' => $this->faker->randomElement([
'დაგვიკავშირდი',
'უფასო კონსულტაცია',
'დაგვირეკე ახლავე',
]),

/* =========================
🟢 FEATURES (MAX 5)
========================= */
'features' => collect(range(1, rand(3, 5)))
->map(fn() => [
'text' => $this->faker->sentence(5),
])
->toArray(),

/* =========================
❓ FAQ
========================= */
'faq' => collect(range(1, rand(2, 4)))
->map(fn() => [
'q' => $this->faker->sentence(),
'a' => $this->faker->paragraph(),
])
->toArray(),

/* =========================
🎯 CTA
========================= */
'cta_title' => $this->faker->randomElement([
'დაგვიკავშირდი დღესვე',
'მიიღე უფასო კონსულტაცია',
'დაიწყე ახლა',
]),

'cta_description' => $this->faker->sentence(10),

/* =========================
🔥 SEO (UNCHANGED)
========================= */
'seo' => [
'title' => $title . ' თბილისში',
'description' => $this->faker->sentence(12),
'keywords' => [
'IT სერვისები თბილისი',
'ქსელები',
'უსაფრთხოება',
],
'content' => collect(range(1, 3))
->map(fn() => $this->faker->paragraph())
->toArray(),
],
];
}
}
