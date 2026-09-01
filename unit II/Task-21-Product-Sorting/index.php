<?php
$products=[
  ['id'=>1,'name'=>'iPhone 15 Pro','category'=>'Smartphones','price'=>134900,'rating'=>4.8,'stock'=>45,'brand'=>'Apple','discount'=>5],
  ['id'=>2,'name'=>'Samsung Galaxy S24','category'=>'Smartphones','price'=>79999,'rating'=>4.6,'stock'=>120,'brand'=>'Samsung','discount'=>10],
  ['id'=>3,'name'=>'MacBook Air M3','category'=>'Laptops','price'=>114900,'rating'=>4.9,'stock'=>30,'brand'=>'Apple','discount'=>3],
  ['id'=>4,'name'=>'Dell XPS 15','category'=>'Laptops','price'=>89990,'rating'=>4.5,'stock'=>25,'brand'=>'Dell','discount'=>8],
  ['id'=>5,'name'=>'Sony WH-1000XM5','category'=>'Audio','price'=>29990,'rating'=>4.8,'stock'=>80,'brand'=>'Sony','discount'=>15],
  ['id'=>6,'name'=>'iPad Pro 12.9','category'=>'Tablets','price'=>112900,'rating'=>4.7,'stock'=>55,'brand'=>'Apple','discount'=>5],
  ['id'=>7,'name'=>'OnePlus 12','category'=>'Smartphones','price'=>64999,'rating'=>4.5,'stock'=>200,'brand'=>'OnePlus','discount'=>12],
  ['id'=>8,'name'=>'LG OLED 55"','category'=>'TVs','price'=>139990,'rating'=>4.7,'stock'=>20,'brand'=>'LG','discount'=>7],
  ['id'=>9,'name'=>'Canon EOS R50','category'=>'Cameras','price'=>74995,'rating'=>4.6,'stock'=>15,'brand'=>'Canon','discount'=>5],
  ['id'=>10,'name'=>'Bose QuietComfort 45','category'=>'Audio','price'=>24990,'rating'=>4.6,'stock'=>65,'brand'=>'Bose','discount'=>20],
  ['id'=>11,'name'=>'Realme GT 5 Pro','category'=>'Smartphones','price'=>35999,'rating'=>4.3,'stock'=>350,'brand'=>'Realme','discount'=>18],
  ['id'=>12,'name'=>'Asus ROG Phone 8','category'=>'Smartphones','price'=>99999,'rating'=>4.7,'stock'=>40,'brand'=>'Asus','discount'=>5],
  ['id'=>13,'name'=>'HP Pavilion 15','category'=>'Laptops','price'=>54990,'rating'=>4.2,'stock'=>90,'brand'=>'HP','discount'=>10],
  ['id'=>14,'name'=>'Samsung 65" QLED','category'=>'TVs','price'=>89990,'rating'=>4.5,'stock'=>30,'brand'=>'Samsung','discount'=>8],
  ['id'=>15,'name'=>'JBL Charge 5','category'=>'Audio','price'=>12999,'rating'=>4.5,'stock'=>150,'brand'=>'JBL','discount'=>15],
  ['id'=>16,'name'=>'Xiaomi Pad 6','category'=>'Tablets','price'=>22999,'rating'=>4.4,'stock'=>100,'brand'=>'Xiaomi','discount'=>12],
  ['id'=>17,'name'=>'GoPro Hero 12','category'=>'Cameras','price'=>39999,'rating'=>4.6,'stock'=>45,'brand'=>'GoPro','discount'=>10],
  ['id'=>18,'name'=>'Nikon Z30','category'=>'Cameras','price'=>60995,'rating'=>4.4,'stock'=>25,'brand'=>'Nikon','discount'=>8],
  ['id'=>19,'name'=>'Apple Watch Series 9','category'=>'Wearables','price'=>41900,'rating'=>4.8,'stock'=>60,'brand'=>'Apple','discount'=>5],
  ['id'=>20,'name'=>'Samsung Galaxy Watch 6','category'=>'Wearables','price'=>24999,'rating'=>4.4,'stock'=>85,'brand'=>'Samsung','discount'=>10]
];

function calculateDiscountedPrice($price, $discount) {
    return $price * (1 - $discount / 100);
}

function renderStars($rating) {
    $full = floor($rating);
    $half = $rating - $full >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;
    
    $stars = str_repeat('<span class="star full">★</span>', $full);
    if ($half) $stars .= '<span class="star half">★</span>';
    $stars .= str_repeat('<span class="star empty">★</span>', $empty);
    
    return $stars . ' <span class="rating-val">(' . $rating . ')</span>';
}

foreach ($products as &$p) {
    $p['discountedPrice'] = calculateDiscountedPrice($p['price'], $p['discount']);
    $p['savings'] = $p['price'] - $p['discountedPrice'];
}
unset($p);

$catFilter = $_GET['category'] ?? '';
$brandFilter = $_GET['brand'] ?? '';
$sortBy = $_GET['sort'] ?? '';

function filterProducts($products, $cat, $brand) {
    return array_filter($products, function($p) use ($cat, $brand) {
        $catMatch = empty($cat) || $p['category'] === $cat;
        $brandMatch = empty($brand) || $p['brand'] === $brand;
        return $catMatch && $brandMatch;
    });
}

function sortProducts(&$products, $sortBy) {
    if ($sortBy === 'price_asc') {
        usort($products, fn($a, $b) => $a['discountedPrice'] <=> $b['discountedPrice']);
    } elseif ($sortBy === 'price_desc') {
        usort($products, fn($a, $b) => $b['discountedPrice'] <=> $a['discountedPrice']);
    } elseif ($sortBy === 'rating_desc') {
        usort($products, fn($a, $b) => $b['rating'] <=> $a['rating']);
    } elseif ($sortBy === 'name_asc') {
        usort($products, fn($a, $b) => strcasecmp($a['name'], $b['name']));
    } elseif ($sortBy === 'discount_desc') {
        usort($products, fn($a, $b) => $b['discount'] <=> $a['discount']);
    } elseif ($sortBy === 'stock_asc') {
        usort($products, fn($a, $b) => $a['stock'] <=> $b['stock']);
    }
}

$filtered = filterProducts($products, $catFilter, $brandFilter);
sortProducts($filtered, $sortBy);

$categories = array_unique(array_column($products, 'category'));
$brands = array_unique(array_column($products, 'brand'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Catalog</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <h1>TechStore</h1>
        </div>
    </header>

    <div class="container layout">
        <aside class="sidebar">
            <form method="GET" id="filterForm">
                <div class="filter-section">
                    <h3>Sort By</h3>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="">Featured</option>
                        <option value="price_asc" <?= $sortBy === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_desc" <?= $sortBy === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="rating_desc" <?= $sortBy === 'rating_desc' ? 'selected' : '' ?>>Highest Rated</option>
                        <option value="discount_desc" <?= $sortBy === 'discount_desc' ? 'selected' : '' ?>>Biggest Discount</option>
                        <option value="name_asc" <?= $sortBy === 'name_asc' ? 'selected' : '' ?>>Name: A-Z</option>
                        <option value="stock_asc" <?= $sortBy === 'stock_asc' ? 'selected' : '' ?>>Low Stock First</option>
                    </select>
                </div>

                <div class="filter-section">
                    <h3>Categories</h3>
                    <div class="pill-group">
                        <label class="pill">
                            <input type="radio" name="category" value="" onchange="this.form.submit()" <?= empty($catFilter) ? 'checked' : '' ?>>
                            <span>All</span>
                        </label>
                        <?php foreach ($categories as $cat): ?>
                        <label class="pill">
                            <input type="radio" name="category" value="<?= htmlspecialchars($cat) ?>" onchange="this.form.submit()" <?= $catFilter === $cat ? 'checked' : '' ?>>
                            <span><?= htmlspecialchars($cat) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-section">
                    <h3>Brands</h3>
                    <div class="pill-group">
                        <label class="pill">
                            <input type="radio" name="brand" value="" onchange="this.form.submit()" <?= empty($brandFilter) ? 'checked' : '' ?>>
                            <span>All</span>
                        </label>
                        <?php foreach ($brands as $br): ?>
                        <label class="pill">
                            <input type="radio" name="brand" value="<?= htmlspecialchars($br) ?>" onchange="this.form.submit()" <?= $brandFilter === $br ? 'checked' : '' ?>>
                            <span><?= htmlspecialchars($br) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <a href="index.php" class="btn-clear">Clear All Filters</a>
            </form>
        </aside>

        <main class="product-grid">
            <?php if (empty($filtered)): ?>
                <div class="no-results">No products found matching your criteria.</div>
            <?php else: ?>
                <?php foreach ($filtered as $item): ?>
                    <div class="product-card">
                        <?php if ($item['discount'] >= 15): ?>
                            <div class="badge-deal">Hot Deal</div>
                        <?php endif; ?>
                        
                        <div class="product-img">
                            <div class="placeholder-img">Image</div>
                        </div>
                        
                        <div class="product-info">
                            <div class="brand"><?= htmlspecialchars($item['brand']) ?></div>
                            <h3 class="name"><?= htmlspecialchars($item['name']) ?></h3>
                            
                            <div class="rating">
                                <?= renderStars($item['rating']) ?>
                            </div>
                            
                            <div class="price-section">
                                <div class="price">₹<?= number_format($item['discountedPrice']) ?></div>
                                <div class="mrp">₹<?= number_format($item['price']) ?></div>
                                <div class="discount"><?= $item['discount'] ?>% OFF</div>
                            </div>
                            
                            <?php if ($item['stock'] < 30): ?>
                                <div class="stock-alert">Only <?= $item['stock'] ?> left in stock!</div>
                            <?php else: ?>
                                <div class="stock-ok">In Stock</div>
                            <?php endif; ?>
                            
                            <button class="btn-add">Add to Cart</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
