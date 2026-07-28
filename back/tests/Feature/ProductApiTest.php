<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_localized_products_categories_and_filters(): void
    {
        $category = ProductCategory::query()->create([
            'name' => 'კამერები',
            'slug' => 'cameras',
            'translations' => [
                'fields' => [
                    'name' => [
                        'ka' => 'კამერები',
                        'en' => 'Cameras',
                        'ru' => 'Камеры',
                    ],
                ],
            ],
        ]);
        $filter = ProductFilter::query()->create([
            'name' => 'ბრენდი',
            'slug' => 'brand',
            'translations' => [
                'fields' => [
                    'name' => [
                        'ka' => 'ბრენდი',
                        'en' => 'Brand',
                        'ru' => 'Бренд',
                    ],
                ],
            ],
            'options' => [
                [
                    'label' => 'Dahua',
                    'slug' => 'dahua',
                    'translations' => [
                        'ka' => 'Dahua',
                        'en' => 'Dahua',
                        'ru' => 'Dahua',
                    ],
                ],
            ],
        ]);
        Product::query()->create([
            'product_category_id' => $category->id,
            'name' => 'უსაფრთხოების კამერა',
            'slug' => 'security-camera',
            'short_description' => '<p>ქართული მოკლე აღწერა</p>',
            'description' => '<p>ქართული სრული აღწერა</p>',
            'price' => null,
            'currency' => 'GEL',
            'filter_values' => [
                [
                    'filter_slug' => $filter->slug,
                    'option_slugs' => ['dahua'],
                ],
            ],
            'seo' => [
                'title' => 'ქართული SEO სათაური',
                'description' => '<p>ქართული SEO აღწერა</p>',
            ],
            'translations' => [
                'fields' => [
                    'name' => [
                        'ka' => 'უსაფრთხოების კამერა',
                        'en' => 'Security Camera',
                        'ru' => 'Камера безопасности',
                    ],
                    'shortDescription' => [
                        'ka' => '<p>ქართული მოკლე აღწერა</p>',
                        'en' => '<p>English short description</p>',
                        'ru' => '<p>Русское краткое описание</p>',
                    ],
                    'description' => [
                        'ka' => '<p>ქართული სრული აღწერა</p>',
                        'en' => '<p>English full description</p>',
                        'ru' => '<p>Русское полное описание</p>',
                    ],
                    'seoTitle' => [
                        'ka' => 'ქართული SEO სათაური',
                        'en' => 'English SEO title',
                        'ru' => 'Русский SEO заголовок',
                    ],
                    'seoDescription' => [
                        'ka' => '<p>ქართული SEO აღწერა</p>',
                        'en' => '<p>English SEO description</p>',
                        'ru' => '<p>Русское SEO описание</p>',
                    ],
                    'imageAlt' => [
                        'ka' => 'კამერის ფოტო',
                        'en' => 'Camera photo',
                        'ru' => 'Фото камеры',
                    ],
                ],
            ],
            'is_published' => true,
        ]);

        $this->getJson('/api/products?locale=en')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'security-camera')
            ->assertJsonPath('data.0.name', 'Security Camera')
            ->assertJsonPath('data.0.contactForPrice', true)
            ->assertJsonPath('data.0.category.name', 'Cameras')
            ->assertJsonPath('data.0.filters.0.name', 'Brand')
            ->assertJsonPath('data.0.filters.0.options.0.name', 'Dahua')
            ->assertJsonPath('data.0.seo.title', 'English SEO title');

        $this->getJson('/api/product-categories?locale=en')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'cameras')
            ->assertJsonPath('data.0.name', 'Cameras');

        $this->getJson('/api/product-filters?locale=en')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'brand')
            ->assertJsonPath('data.0.name', 'Brand')
            ->assertJsonPath('data.0.options.0.slug', 'dahua')
            ->assertJsonPath('data.0.options.0.count', 1);

        $this->getJson('/api/products/security-camera?locale=ru')
            ->assertOk()
            ->assertJsonPath('data.name', 'Камера безопасности')
            ->assertJsonPath('data.seo.title', 'Русский SEO заголовок');
    }

    public function test_product_filters_and_categories_only_expose_published_content(): void
    {
        $category = ProductCategory::query()->create([
            'name' => 'Switches',
            'slug' => 'switches',
        ]);
        $filter = ProductFilter::query()->create([
            'name' => 'Series',
            'slug' => 'series',
            'options' => [
                ['label' => 'Pro', 'slug' => 'pro'],
            ],
        ]);
        Product::query()->create([
            'product_category_id' => $category->id,
            'name' => 'Hidden switch',
            'slug' => 'hidden-switch',
            'short_description' => 'Hidden short description',
            'description' => 'Hidden long description',
            'filter_values' => [
                ['filter_slug' => 'series', 'option_slugs' => ['pro']],
            ],
            'is_published' => false,
        ]);

        $this->getJson('/api/products')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/product-categories')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/product-filters')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_product_list_can_be_filtered_by_category_and_filter_values(): void
    {
        $cameraCategory = ProductCategory::query()->create([
            'name' => 'Cameras',
            'slug' => 'cameras',
        ]);
        $networkCategory = ProductCategory::query()->create([
            'name' => 'Networking',
            'slug' => 'networking',
        ]);
        ProductFilter::query()->create([
            'name' => 'Brand',
            'slug' => 'brand',
            'options' => [
                ['label' => 'Dahua', 'slug' => 'dahua'],
                ['label' => 'Ubiquiti', 'slug' => 'ubiquiti'],
            ],
        ]);
        Product::query()->create([
            'product_category_id' => $cameraCategory->id,
            'name' => 'Camera A',
            'slug' => 'camera-a',
            'short_description' => 'Short description A',
            'description' => 'Long description A',
            'filter_values' => [
                ['filter_slug' => 'brand', 'option_slugs' => ['dahua']],
            ],
            'is_published' => true,
        ]);
        Product::query()->create([
            'product_category_id' => $networkCategory->id,
            'name' => 'Switch B',
            'slug' => 'switch-b',
            'short_description' => 'Short description B',
            'description' => 'Long description B',
            'filter_values' => [
                ['filter_slug' => 'brand', 'option_slugs' => ['ubiquiti']],
            ],
            'is_published' => true,
        ]);

        $this->getJson('/api/products?category=cameras')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'camera-a');

        $this->getJson('/api/products?filter_brand=dahua')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'camera-a');
    }

    public function test_product_list_matches_filter_groups_and_multi_option_values_correctly(): void
    {
        $category = ProductCategory::query()->create([
            'name' => 'Cameras',
            'slug' => 'cameras',
        ]);

        ProductFilter::query()->create([
            'name' => 'Brand',
            'slug' => 'brand',
            'options' => [
                ['label' => 'Dahua', 'slug' => 'dahua'],
                ['label' => 'Hikvision', 'slug' => 'hikvision'],
            ],
        ]);

        ProductFilter::query()->create([
            'name' => 'Placement',
            'slug' => 'placement',
            'options' => [
                ['label' => 'Outdoor', 'slug' => 'outdoor'],
                ['label' => 'Indoor', 'slug' => 'indoor'],
            ],
        ]);

        Product::query()->create([
            'product_category_id' => $category->id,
            'name' => 'Outdoor Camera',
            'slug' => 'outdoor-camera',
            'short_description' => 'Outdoor short description',
            'description' => 'Outdoor long description',
            'filter_values' => [
                ['filter_slug' => 'brand', 'option_slugs' => ['dahua', 'hikvision']],
                ['filter_slug' => 'placement', 'option_slugs' => ['outdoor']],
            ],
            'is_published' => true,
        ]);

        Product::query()->create([
            'product_category_id' => $category->id,
            'name' => 'Indoor Camera',
            'slug' => 'indoor-camera',
            'short_description' => 'Indoor short description',
            'description' => 'Indoor long description',
            'filter_values' => [
                ['filter_slug' => 'brand', 'option_slugs' => ['dahua']],
                ['filter_slug' => 'placement', 'option_slugs' => ['indoor']],
            ],
            'is_published' => true,
        ]);

        Product::query()->create([
            'product_category_id' => $category->id,
            'name' => 'Accessory Pack',
            'slug' => 'accessory-pack',
            'short_description' => 'Accessory short description',
            'description' => 'Accessory long description',
            'filter_values' => [
                ['filter_slug' => 'brand', 'option_slugs' => ['hikvision']],
                ['filter_slug' => 'placement', 'option_slugs' => ['dahua']],
            ],
            'is_published' => true,
        ]);

        $this->getJson('/api/products?filter_brand=dahua&filter_placement=outdoor')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'outdoor-camera');

        $this->getJson('/api/products?filter_brand=dahua,hikvision')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_missing_product_slug_returns_a_clean_not_found_response(): void
    {
        $this->getJson('/api/products/no-such-product')
            ->assertNotFound()
            ->assertExactJson([
                'message' => 'Product not found.',
            ]);
    }

    public function test_product_endpoints_remain_stable_when_product_tables_are_missing(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_filters');
        Schema::dropIfExists('product_categories');

        $this->getJson('/api/products')
            ->assertOk()
            ->assertExactJson([
                'data' => [],
            ]);

        $this->getJson('/api/product-categories')
            ->assertOk()
            ->assertExactJson([
                'data' => [],
            ]);

        $this->getJson('/api/product-filters')
            ->assertOk()
            ->assertExactJson([
                'data' => [],
            ]);

        $this->getJson('/api/products/no-such-product')
            ->assertNotFound()
            ->assertExactJson([
                'message' => 'Product not found.',
            ]);
    }
}
