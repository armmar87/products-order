<?php
namespace Database\Factories;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Models\ProductInventory;
use App\Models\ProductSeo;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(rand(2, 4), true);
        $name = ucwords($name);
        $price = fake()->randomFloat(2, 9.99, 999.99);
        $comparePrice = rand(0, 1) ? $price + fake()->randomFloat(2, 5, 100) : null;
        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'brand_id' => Brand::inRandomOrder()->first()?->id ?? Brand::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(6),
            'description' => fake()->paragraphs(rand(2, 4), true),
            'price' => $price,
            'compare_price' => $comparePrice,
            'weight' => fake()->randomFloat(2, 0.1, 50),
            'status' => fake()->randomElement(['active', 'active', 'active', 'inactive', 'draft']),
            'is_featured' => fake()->boolean(20), // 20% chance of being featured
            'views' => fake()->numberBetween(0, 10000),
            'sales_count' => fake()->numberBetween(0, 500),
        ];
    }
    /**
     * Configure the model factory to create related records.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            // Create inventory
            ProductInventory::create([
                'product_id' => $product->id,
                'sku' => 'SKU-' . strtoupper(Str::random(8)),
                'barcode' => fake()->boolean(70) ? fake()->ean13() : null,
                'stock' => fake()->numberBetween(0, 500),
                'min_stock' => fake()->numberBetween(5, 20),
                'reserved_stock' => 0,
            ]);
            // Create SEO data
            ProductSeo::create([
                'product_id' => $product->id,
                'meta_title' => fake()->boolean(80) ? $product->name . ' - ' . fake()->words(3, true) : null,
                'meta_description' => fake()->boolean(80) ? fake()->sentence(20) : null,
                'meta_keywords' => fake()->boolean(60) ? implode(', ', fake()->words(5)) : null,
            ]);
            // Create 1-5 images
            $imageCount = rand(1, 5);
            for ($i = 0; $i < $imageCount; $i++) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => 'products/' . fake()->uuid() . '.jpg',
                    'alt_text' => $product->name . ' - Image ' . ($i + 1),
                    'sort_order' => $i,
                    'is_primary' => $i === 0,
                ]);
            }
            // Create 2-6 attributes
            $attributeCount = rand(2, 6);
            $attributeNames = ['Color', 'Size', 'Material', 'Brand', 'Weight', 'Dimensions', 'Warranty', 'Model'];
            shuffle($attributeNames);
            for ($i = 0; $i < $attributeCount; $i++) {
                ProductAttribute::create([
                    'product_id' => $product->id,
                    'attribute_name' => $attributeNames[$i],
                    'attribute_value' => $this->getAttributeValue($attributeNames[$i]),
                    'sort_order' => $i,
                ]);
            }
            // Attach 1-5 random tags
            $tagCount = rand(1, 5);
            $tags = Tag::inRandomOrder()->limit($tagCount)->pluck('id');
            if ($tags->isEmpty()) {
                // Create some tags if none exist
                $tagNames = ['Electronics', 'Fashion', 'Home', 'Sports', 'Books', 'Toys', 'Food', 'Beauty'];
                foreach (array_slice($tagNames, 0, $tagCount) as $tagName) {
                    $tag = Tag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName]
                    );
                    $tags->push($tag->id);
                }
            }
            $product->tags()->attach($tags);
        });
    }
    /**
     * Generate attribute value based on attribute name.
     */
    private function getAttributeValue(string $attributeName): string
    {
        return match ($attributeName) {
            'Color' => fake()->safeColorName(),
            'Size' => fake()->randomElement(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
            'Material' => fake()->randomElement(['Cotton', 'Polyester', 'Leather', 'Metal', 'Plastic', 'Wood']),
            'Brand' => fake()->company(),
            'Weight' => fake()->randomFloat(2, 0.1, 10) . ' kg',
            'Dimensions' => fake()->numberBetween(10, 100) . 'x' . fake()->numberBetween(10, 100) . 'x' . fake()->numberBetween(10, 100) . ' cm',
            'Warranty' => fake()->randomElement(['1 year', '2 years', '3 years', '5 years', 'Lifetime']),
            'Model' => strtoupper(fake()->bothify('??-####')),
            default => fake()->word(),
        };
    }
    /**
     * Indicate that the product should be featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
    /**
     * Indicate that the product should be active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }
    /**
     * Indicate that the product should be out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'out_of_stock',
        ]);
    }
    /**
     * Indicate that the product should be a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
}
