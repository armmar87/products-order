<?php
namespace Database\Seeders;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ProductSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting product seeding...');
        // Create initial data first
        $this->createInitialData();
        // Seed products
        $totalProducts = 100_000_000; // 10 million
        $chunkSize = 1000; // Process 1000 at a time
        $chunks = (int) ceil($totalProducts / $chunkSize);
        $this->command->info("📦 Seeding {$totalProducts} products in {$chunks} chunks of {$chunkSize}...");
        $this->command->warn('⚠️  This will take a VERY long time (hours/days)!');
        $this->command->newLine();
        $bar = $this->command->getOutput()->createProgressBar($chunks);
        $bar->start();
        // Disable model events for better performance
        Product::unsetEventDispatcher();
        for ($i = 0; $i < $chunks; $i++) {
            // Use transaction for each chunk
            DB::transaction(function () use ($chunkSize) {
                Product::factory()
                    ->count($chunkSize)
                    ->create();
            });
            $bar->advance();
            // Clear memory every 100 chunks
            if (($i + 1) % 100 === 0) {
                $this->clearMemory();
            }
        }
        $bar->finish();
        $this->command->newLine(2);
        $this->command->info("✅ Successfully seeded {$totalProducts} products!");
        // Show statistics
        $this->showStatistics();
    }
    /**
     * Create initial categories, brands, and tags.
     */
    private function createInitialData(): void
    {
        $this->command->info('📁 Creating categories...');
        Category::factory()->count(100)->create();
        $this->command->info('🏷️  Creating brands...');
        Brand::factory()->count(200)->create();
        $this->command->info('🏷️  Creating tags...');
        $tags = [
            'Electronics', 'Computers', 'Phones', 'Tablets', 'Laptops',
            'Fashion', 'Men', 'Women', 'Kids', 'Accessories',
            'Home', 'Furniture', 'Kitchen', 'Garden', 'Decor',
            'Sports', 'Fitness', 'Outdoor', 'Indoor', 'Equipment',
            'Books', 'Fiction', 'Non-Fiction', 'Educational', 'Comics',
            'Toys', 'Games', 'Puzzles', 'Action Figures', 'Dolls',
            'Food', 'Beverages', 'Snacks', 'Organic', 'Gourmet',
            'Beauty', 'Skincare', 'Makeup', 'Haircare', 'Fragrance',
            'Automotive', 'Parts', 'Tools', 'Accessories', 'Maintenance',
            'Office', 'Supplies', 'Furniture', 'Electronics', 'Organization',
        ];
        foreach ($tags as $tagName) {
            Tag::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($tagName)],
                ['name' => $tagName]
            );
        }
        $this->command->newLine();
    }
    /**
     * Clear memory to prevent memory exhaustion.
     */
    private function clearMemory(): void
    {
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }
    /**
     * Show seeding statistics.
     */
    private function showStatistics(): void
    {
        $this->command->info('📊 Database Statistics:');
        $this->command->table(
            ['Table', 'Count'],
            [
                ['Products', number_format(Product::count())],
                ['Categories', Category::count()],
                ['Brands', Brand::count()],
                ['Tags', Tag::count()],
                ['Product Inventory', number_format(DB::table('product_inventory')->count())],
                ['Product Images', number_format(DB::table('product_images')->count())],
                ['Product Attributes', number_format(DB::table('product_attributes')->count())],
                ['Product SEO', number_format(DB::table('product_seo')->count())],
            ]
        );
    }
}
