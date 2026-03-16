<?php

declare(strict_types=1);

namespace App\Catalog\Database\Seeders;

use App\Catalog\Enums\ProductType;
use App\Catalog\Models\Attribute;
use App\Catalog\Models\AttributeOption;
use App\Catalog\Models\AttributeSet;
use App\Catalog\Models\AttributeValue;
use App\Catalog\Models\Category;
use App\Catalog\Models\Product;
use Illuminate\Database\Seeder;
use Quicktane\Inventory\Models\InventorySource;
use Quicktane\Inventory\Models\StockItem;
use Quicktane\Tax\Models\TaxClass;

class SampleProductSeeder extends Seeder
{
    public function run(): void
    {
        $defaultAttributeSet = AttributeSet::where('name', 'Default')->first();
        $clothingAttributeSet = AttributeSet::where('name', 'Clothing')->first();
        $taxableGoods = TaxClass::where('name', 'Taxable Goods')->first();
        $defaultSource = InventorySource::where('code', 'default-warehouse')->first();

        if ($defaultAttributeSet === null || $taxableGoods === null || $defaultSource === null) {
            return;
        }

        $colorAttribute = Attribute::where('code', 'color')->first();
        $sizeAttribute = Attribute::where('code', 'size')->first();
        $materialAttribute = Attribute::where('code', 'material')->first();
        $brandAttribute = Attribute::where('code', 'brand')->first();

        $products = [
            [
                'sku' => 'MT-001',
                'name' => 'Classic Cotton T-Shirt',
                'slug' => 'classic-cotton-t-shirt',
                'description' => 'A comfortable everyday cotton t-shirt with a relaxed fit. Perfect for casual wear.',
                'short_description' => 'Comfortable cotton t-shirt for everyday wear.',
                'base_price' => 29.99,
                'cost' => 12.00,
                'weight' => 0.25,
                'category_slug' => 'men-t-shirts',
                'attribute_set' => $clothingAttributeSet,
                'attributes' => ['color' => 'black', 'size' => 'm', 'material' => 'cotton', 'brand' => 'QuikBasics'],
                'stock' => 150,
            ],
            [
                'sku' => 'MT-002',
                'name' => 'Premium V-Neck T-Shirt',
                'slug' => 'premium-v-neck-t-shirt',
                'description' => 'Soft premium fabric V-neck t-shirt. Slightly tapered fit for a modern look.',
                'short_description' => 'Premium V-neck tee with modern fit.',
                'base_price' => 39.99,
                'special_price' => 34.99,
                'cost' => 16.00,
                'weight' => 0.22,
                'category_slug' => 'men-t-shirts',
                'attribute_set' => $clothingAttributeSet,
                'attributes' => ['color' => 'white', 'size' => 'l', 'material' => 'cotton', 'brand' => 'QuikBasics'],
                'stock' => 85,
            ],
            [
                'sku' => 'MJ-001',
                'name' => 'Slim Fit Dark Denim Jeans',
                'slug' => 'slim-fit-dark-denim-jeans',
                'description' => 'Classic slim fit jeans in dark wash denim. Five-pocket styling with zip fly.',
                'short_description' => 'Slim fit dark wash denim jeans.',
                'base_price' => 79.99,
                'cost' => 32.00,
                'weight' => 0.85,
                'category_slug' => 'men-jeans',
                'attribute_set' => $clothingAttributeSet,
                'attributes' => ['color' => 'blue', 'size' => 'l', 'material' => 'cotton', 'brand' => 'DenimCraft'],
                'stock' => 60,
            ],
            [
                'sku' => 'MJ-002',
                'name' => 'Relaxed Fit Cargo Jeans',
                'slug' => 'relaxed-fit-cargo-jeans',
                'description' => 'Relaxed fit cargo jeans with multiple pockets. Comfortable and durable.',
                'short_description' => 'Relaxed cargo jeans with extra pockets.',
                'base_price' => 89.99,
                'special_price' => 69.99,
                'cost' => 35.00,
                'weight' => 0.95,
                'category_slug' => 'men-jeans',
                'attribute_set' => $clothingAttributeSet,
                'attributes' => ['color' => 'green', 'size' => 'xl', 'material' => 'cotton', 'brand' => 'DenimCraft'],
                'stock' => 45,
            ],
            [
                'sku' => 'MK-001',
                'name' => 'Leather Biker Jacket',
                'slug' => 'leather-biker-jacket',
                'description' => 'Genuine leather biker jacket with asymmetric zip. A timeless classic.',
                'short_description' => 'Classic leather biker jacket.',
                'base_price' => 299.99,
                'cost' => 120.00,
                'weight' => 1.80,
                'category_slug' => 'men-jackets',
                'attribute_set' => $clothingAttributeSet,
                'attributes' => ['color' => 'black', 'size' => 'l', 'material' => 'leather', 'brand' => 'UrbanEdge'],
                'stock' => 20,
            ],
            [
                'sku' => 'MK-002',
                'name' => 'Wool Blend Overcoat',
                'slug' => 'wool-blend-overcoat',
                'description' => 'Elegant wool blend overcoat for colder months. Tailored fit with notch lapel.',
                'short_description' => 'Tailored wool blend overcoat.',
                'base_price' => 249.99,
                'cost' => 95.00,
                'weight' => 1.50,
                'category_slug' => 'men-jackets',
                'attribute_set' => $clothingAttributeSet,
                'attributes' => ['color' => 'black', 'size' => 'xl', 'material' => 'wool', 'brand' => 'UrbanEdge'],
                'stock' => 30,
            ],
            [
                'sku' => 'WD-001',
                'name' => 'Floral Summer Dress',
                'slug' => 'floral-summer-dress',
                'description' => 'Light and airy floral print summer dress. Features adjustable straps and flowing skirt.',
                'short_description' => 'Floral print summer dress.',
                'base_price' => 59.99,
                'cost' => 22.00,
                'weight' => 0.30,
                'category_slug' => 'women-dresses',
                'attribute_set' => $clothingAttributeSet,
                'attributes' => ['color' => 'red', 'size' => 's', 'material' => 'polyester', 'brand' => 'FloraStyle'],
                'stock' => 70,
            ],
            [
                'sku' => 'WD-002',
                'name' => 'Silk Evening Dress',
                'slug' => 'silk-evening-dress',
                'description' => 'Luxurious silk evening dress with elegant draping. Perfect for formal events.',
                'short_description' => 'Elegant silk evening dress.',
                'base_price' => 189.99,
                'special_price' => 159.99,
                'cost' => 75.00,
                'weight' => 0.45,
                'category_slug' => 'women-dresses',
                'attribute_set' => $clothingAttributeSet,
                'attributes' => ['color' => 'black', 'size' => 'm', 'material' => 'silk', 'brand' => 'FloraStyle'],
                'stock' => 25,
            ],
            [
                'sku' => 'WT-001',
                'name' => 'Casual Blouse',
                'slug' => 'casual-blouse',
                'description' => 'Lightweight casual blouse with a relaxed fit. Great for layering.',
                'short_description' => 'Lightweight casual blouse.',
                'base_price' => 44.99,
                'cost' => 18.00,
                'weight' => 0.20,
                'category_slug' => 'women-tops',
                'attribute_set' => $clothingAttributeSet,
                'attributes' => ['color' => 'white', 'size' => 's', 'material' => 'polyester', 'brand' => 'FloraStyle'],
                'stock' => 90,
            ],
            [
                'sku' => 'WT-002',
                'name' => 'Knit Turtleneck Sweater',
                'slug' => 'knit-turtleneck-sweater',
                'description' => 'Cozy knit turtleneck sweater in soft wool blend. Perfect for autumn and winter.',
                'short_description' => 'Cozy knit turtleneck sweater.',
                'base_price' => 69.99,
                'cost' => 28.00,
                'weight' => 0.55,
                'category_slug' => 'women-tops',
                'attribute_set' => $clothingAttributeSet,
                'attributes' => ['color' => 'red', 'size' => 'm', 'material' => 'wool', 'brand' => 'FloraStyle'],
                'stock' => 55,
            ],
            [
                'sku' => 'AB-001',
                'name' => 'Leather Crossbody Bag',
                'slug' => 'leather-crossbody-bag',
                'description' => 'Compact leather crossbody bag with adjustable strap and multiple compartments.',
                'short_description' => 'Compact leather crossbody bag.',
                'base_price' => 129.99,
                'cost' => 50.00,
                'weight' => 0.60,
                'category_slug' => 'accessories-bags',
                'attribute_set' => $defaultAttributeSet,
                'attributes' => ['color' => 'black', 'brand' => 'UrbanEdge'],
                'stock' => 40,
            ],
            [
                'sku' => 'AB-002',
                'name' => 'Canvas Tote Bag',
                'slug' => 'canvas-tote-bag',
                'description' => 'Spacious canvas tote bag with reinforced handles. Perfect for shopping and daily use.',
                'short_description' => 'Spacious canvas tote bag.',
                'base_price' => 34.99,
                'cost' => 14.00,
                'weight' => 0.40,
                'category_slug' => 'accessories-bags',
                'attribute_set' => $defaultAttributeSet,
                'attributes' => ['color' => 'green', 'brand' => 'EcoCarry'],
                'stock' => 120,
            ],
            [
                'sku' => 'AW-001',
                'name' => 'Stainless Steel Chronograph Watch',
                'slug' => 'stainless-steel-chronograph-watch',
                'description' => 'Classic stainless steel chronograph watch with sapphire crystal glass and water resistance.',
                'short_description' => 'Stainless steel chronograph watch.',
                'base_price' => 199.99,
                'special_price' => 179.99,
                'cost' => 80.00,
                'weight' => 0.15,
                'category_slug' => 'accessories-watches',
                'attribute_set' => $defaultAttributeSet,
                'attributes' => ['brand' => 'TimeCraft'],
                'stock' => 35,
            ],
            [
                'sku' => 'AW-002',
                'name' => 'Minimalist Leather Watch',
                'slug' => 'minimalist-leather-watch',
                'description' => 'Clean minimalist design with genuine leather strap and Japanese quartz movement.',
                'short_description' => 'Minimalist leather strap watch.',
                'base_price' => 149.99,
                'cost' => 55.00,
                'weight' => 0.10,
                'category_slug' => 'accessories-watches',
                'attribute_set' => $defaultAttributeSet,
                'attributes' => ['brand' => 'TimeCraft'],
                'stock' => 50,
            ],
            [
                'sku' => 'ES-001',
                'name' => 'ProMax Smartphone 15',
                'slug' => 'promax-smartphone-15',
                'description' => '6.7-inch OLED display, 256GB storage, 48MP triple camera system, all-day battery life.',
                'short_description' => 'Flagship smartphone with OLED display.',
                'base_price' => 999.99,
                'cost' => 650.00,
                'weight' => 0.22,
                'category_slug' => 'electronics-smartphones',
                'attribute_set' => $defaultAttributeSet,
                'attributes' => ['brand' => 'TechVibe'],
                'stock' => 75,
            ],
            [
                'sku' => 'ES-002',
                'name' => 'Budget Smartphone A5',
                'slug' => 'budget-smartphone-a5',
                'description' => '6.5-inch LCD display, 128GB storage, 12MP dual camera. Great value for money.',
                'short_description' => 'Affordable smartphone with great specs.',
                'base_price' => 299.99,
                'special_price' => 249.99,
                'cost' => 180.00,
                'weight' => 0.19,
                'category_slug' => 'electronics-smartphones',
                'attribute_set' => $defaultAttributeSet,
                'attributes' => ['brand' => 'TechVibe'],
                'stock' => 200,
            ],
            [
                'sku' => 'EL-001',
                'name' => 'UltraBook Pro 14',
                'slug' => 'ultrabook-pro-14',
                'description' => '14-inch Retina display, M3 chip, 16GB RAM, 512GB SSD. Ultra-thin and lightweight.',
                'short_description' => 'Ultra-thin 14-inch professional laptop.',
                'base_price' => 1499.99,
                'cost' => 950.00,
                'weight' => 1.40,
                'category_slug' => 'electronics-laptops',
                'attribute_set' => $defaultAttributeSet,
                'attributes' => ['brand' => 'TechVibe'],
                'stock' => 30,
            ],
            [
                'sku' => 'EL-002',
                'name' => 'Gaming Laptop X7',
                'slug' => 'gaming-laptop-x7',
                'description' => '17.3-inch 144Hz display, RTX 4070, 32GB RAM, 1TB SSD. Built for gamers.',
                'short_description' => 'High-performance 17-inch gaming laptop.',
                'base_price' => 1899.99,
                'special_price' => 1799.99,
                'cost' => 1200.00,
                'weight' => 2.80,
                'category_slug' => 'electronics-laptops',
                'attribute_set' => $defaultAttributeSet,
                'attributes' => ['brand' => 'PowerForge'],
                'stock' => 15,
            ],
            [
                'sku' => 'EH-001',
                'name' => 'Wireless Noise-Cancelling Headphones',
                'slug' => 'wireless-noise-cancelling-headphones',
                'description' => 'Premium over-ear headphones with active noise cancellation. 30-hour battery life.',
                'short_description' => 'Premium wireless noise-cancelling headphones.',
                'base_price' => 349.99,
                'cost' => 140.00,
                'weight' => 0.35,
                'category_slug' => 'electronics-headphones',
                'attribute_set' => $defaultAttributeSet,
                'attributes' => ['color' => 'black', 'brand' => 'SoundWave'],
                'stock' => 65,
            ],
            [
                'sku' => 'EH-002',
                'name' => 'Sport Wireless Earbuds',
                'slug' => 'sport-wireless-earbuds',
                'description' => 'Sweat-resistant wireless earbuds with secure fit. 8-hour battery with charging case.',
                'short_description' => 'Sweat-resistant sport wireless earbuds.',
                'base_price' => 89.99,
                'cost' => 35.00,
                'weight' => 0.05,
                'category_slug' => 'electronics-headphones',
                'attribute_set' => $defaultAttributeSet,
                'attributes' => ['color' => 'white', 'brand' => 'SoundWave'],
                'stock' => 110,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::firstOrCreate(
                ['sku' => $productData['sku']],
                [
                    'type' => ProductType::Simple,
                    'attribute_set_id' => ($productData['attribute_set'] ?? $defaultAttributeSet)->id,
                    'name' => $productData['name'],
                    'slug' => $productData['slug'],
                    'description' => $productData['description'],
                    'short_description' => $productData['short_description'],
                    'base_price' => $productData['base_price'],
                    'special_price' => $productData['special_price'] ?? null,
                    'cost' => $productData['cost'],
                    'weight' => $productData['weight'],
                    'is_active' => true,
                    'tax_class_id' => $taxableGoods->id,
                ],
            );

            if ($product->wasRecentlyCreated) {
                $this->attachCategory($product, $productData['category_slug']);
                $this->createAttributeValues($product, $productData['attributes']);
                $this->createStockItem($product, $defaultSource, $productData['stock']);
            }
        }
    }

    private function attachCategory(Product $product, string $categorySlug): void
    {
        $category = Category::where('slug', $categorySlug)->first();

        if ($category !== null) {
            $product->categories()->syncWithoutDetaching([
                $category->id => ['position' => 0],
            ]);
        }
    }

    private function createAttributeValues(Product $product, array $attributes): void
    {
        foreach ($attributes as $code => $value) {
            $attribute = Attribute::where('code', $code)->first();

            if ($attribute === null) {
                continue;
            }

            $storedValue = $value;

            if ($attribute->type === 'select') {
                $option = AttributeOption::where('attribute_id', $attribute->id)
                    ->where('value', $value)
                    ->first();

                $storedValue = $option?->id ? (string) $option->id : $value;
            }

            AttributeValue::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'attribute_id' => $attribute->id,
                ],
                [
                    'value' => $storedValue,
                ],
            );
        }
    }

    private function createStockItem(Product $product, InventorySource $source, int $quantity): void
    {
        StockItem::firstOrCreate(
            [
                'product_id' => $product->id,
                'source_id' => $source->id,
            ],
            [
                'quantity' => $quantity,
                'reserved' => 0,
                'notify_quantity' => 5,
                'is_in_stock' => true,
            ],
        );
    }
}
