<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Military Collectibles Store') ?></title>

    <link rel="stylesheet" href="<?= site_url('assets/css/root.css') ?>">
    <link rel="stylesheet" href="<?= site_url('assets/css/header.css') ?>">
    <link rel="stylesheet" href="<?= site_url('assets/css/homePage.css') ?>">
    <link rel="stylesheet" href="<?= site_url('assets/css/form.css') ?>">
    <link rel="stylesheet" href="<?= site_url('assets/css/image-form.css') ?>">
    <link rel="stylesheet" href="<?= site_url('assets/css/admin-dash.css') ?>">
    <link rel="stylesheet" href="<?= site_url('assets/css/edit-dash.css') ?>">
    <link rel="stylesheet" href="<?= site_url('assets/css/edit-category.css') ?>">
    <link rel="stylesheet" href="<?= site_url('assets/css/product-cards.css') ?>">

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
</head>
<body>

<!-- Top Header -->
<header class="header-top">
    <div class="header-container">
        <a href="<?= site_url('/') ?>" class="logo" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">
            <span class="logo-text">Logo</span>
        </a>

        <div class="search-container">
            <form action="<?= site_url('search') ?>" method="get">
                <input type="text" name="q" class="search-input" placeholder="Search Site">
                <button type="submit" class="search-btn">
                    <svg class="search-icon" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" fill="none"/>
                        <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </form>
        </div>

        <?php if (!empty($userName)): ?>
            <a href="<?= site_url('account') ?>" class="account-section" style="text-decoration: none;">
                <div class="account-greeting">Hello, <?= esc($userName) ?></div>
                <div class="account-action">My Account</div>
            </a>
        <?php else: ?>
            <a href="<?= site_url('login') ?>" class="account-section" style="text-decoration: none;">
                <div class="account-greeting">Hello, Sign In</div>
                <div class="account-action">Login or Create your Account</div>
            </a>
        <?php endif; ?>

        <a href="<?= site_url('cart') ?>" class="cart-section" style="text-decoration: none;">
            <span class="cart-count"><?= esc($cartCount ?? 0) ?></span>
            <svg class="cart-icon" viewBox="0 0 24 24">
                <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
            </svg>
        </a>

        <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
            <div class="hamburger"></div>
            <span class="x-symbol">✕</span>
        </button>
    </div>
</header>

<!-- Navigation Bar (Desktop) -->
<nav class="nav-bar">
    <div class="nav-container">
        <ul class="nav-list">
            <li class="nav-item">
                <a href="#" class="nav-link">Products</a>
                <?php if (!empty($navigationData)): ?>
                <div class="mega-menu">
                    <div class="mega-menu-content">
                        <div class="category-sidebar">
                            <?php foreach ($navigationData as $index => $parent): ?>
                            <div class="category-item <?= $index === 0 ? 'active' : '' ?>">
                                <a href="<?= site_url('products/category/' . esc($parent['slug'])) ?>" class="category-link">
                                    <?= esc($parent['name']) ?>
                                    <?php if (!empty($parent['children'])): ?>
                                    <svg class="category-arrow" viewBox="0 0 24 24">
                                        <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" fill="none"/>
                                    </svg>
                                    <?php endif; ?>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="subcategory-panel">
                            <?php 
                            // Display first parent's children by default
                            if (!empty($navigationData[0])): 
                                $firstParent = $navigationData[0];
                            ?>
                            <div class="subcategory-column">
                                <h3><?= esc($firstParent['name']) ?></h3>
                                <ul class="subcategory-list">
                                    <li>
                                        <a href="<?= site_url('products/category/' . esc($firstParent['slug'])) ?>" class="subcategory-link">
                                            View All <?= esc($firstParent['name']) ?>
                                        </a>
                                    </li>
                                    <?php if (!empty($firstParent['children'])): ?>
                                        <?php foreach ($firstParent['children'] as $child): ?>
                                        <li>
                                            <a href="<?= site_url('products/category/' . esc($child['slug'])) ?>" class="subcategory-link">
                                                <?= esc($child['name']) ?>
                                            </a>
                                        </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('featured') ?>" class="nav-link">Featured Items</a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('deals') ?>" class="nav-link">Top Deals</a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('faq') ?>" class="nav-link">FAQ</a>
            </li>
        </ul>
    </div>
</nav>

<!-- Mobile Dropdown Menu -->
<div class="mobile-dropdown" id="mobileMenu">
    <div class="mobile-nav-item">
        <a href="#" class="mobile-nav-link" onclick="toggleSubmenu(event, 'products')">
            Products
            <svg class="category-arrow" viewBox="0 0 24 24" width="16" height="16">
                <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" fill="none"/>
            </svg>
        </a>
        <?php if (!empty($navigationData)): ?>
        <div class="mobile-submenu" id="products-submenu">
            <?php foreach ($navigationData as $parent): ?>
            <div class="mobile-submenu-item">
                <a href="<?= site_url('category/' . esc($parent['slug'])) ?>" class="mobile-submenu-link">
                    <?= esc($parent['name']) ?>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="mobile-nav-item">
        <a href="<?= site_url('featured') ?>" class="mobile-nav-link">Featured Items</a>
    </div>
    <div class="mobile-nav-item">
        <a href="<?= site_url('deals') ?>" class="mobile-nav-link">Top Deals</a>
    </div>
    <div class="mobile-nav-item">
        <a href="<?= site_url('faq') ?>" class="mobile-nav-link">FAQ</a>
    </div>
</div>

<script>
    document.getElementById('menuToggle').addEventListener('click', function() {
        this.classList.toggle('active');
    });

</script>


<script>
    function toggleMobileMenu() {
        const btn = document.querySelector('.mobile-menu-btn');
        const menu = document.getElementById('mobileMenu');
        btn.classList.toggle('active');
        menu.classList.toggle('active');
    }

    function toggleSubmenu(event, id) {
        event.preventDefault();
        const submenu = document.getElementById(id + '-submenu');
        submenu.classList.toggle('active');
    }

    document.addEventListener('click', function(event) {
        const menu = document.getElementById('mobileMenu');
        const btn = document.querySelector('.mobile-menu-btn');
        
        if (menu && btn && !menu.contains(event.target) && !btn.contains(event.target)) {
            menu.classList.remove('active');
            btn.classList.remove('active');
        }
    });
</script>

<script>
    // Desktop mega menu functionality
    document.addEventListener('DOMContentLoaded', function() {
        const categoryItems = document.querySelectorAll('.category-item');
        const subcategoryPanel = document.querySelector('.subcategory-panel');
        
        // Store navigation data in a format we can access
        const navigationData = <?= json_encode($navigationData ?? []) ?>;
        
        categoryItems.forEach((item, index) => {
            item.addEventListener('mouseenter', function() {
                // Remove active class from all items
                categoryItems.forEach(i => i.classList.remove('active'));
                // Add active to current
                this.classList.add('active');
                
                // Get the category data for this item
                const categoryData = navigationData[index];
                
                if (categoryData && subcategoryPanel) {
                    // Build the subcategory HTML
                    let html = '<div class="subcategory-column">';
                    html += '<h3>' + escapeHtml(categoryData.name) + '</h3>';
                    html += '<ul class="subcategory-list">';
                    html += '<li><a href="<?= site_url("category/") ?>' + categoryData.slug + '" class="subcategory-link">';
                    html += 'View All ' + escapeHtml(categoryData.name);
                    html += '</a></li>';
                    
                    // Add children if they exist
                    if (categoryData.children && categoryData.children.length > 0) {
                        categoryData.children.forEach(child => {
                            html += '<li><a href="<?= site_url("category/") ?>' + child.slug + '" class="subcategory-link">';
                            html += escapeHtml(child.name);
                            html += '</a></li>';
                        });
                    }
                    
                    html += '</ul></div>';
                    
                    // Update the panel
                    subcategoryPanel.innerHTML = html;
                }
            });
        });
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    });

    // Mobile menu functionality
    function toggleMobileMenu() {
        const btn = document.querySelector('.mobile-menu-btn');
        const menu = document.getElementById('mobileMenu');
        btn.classList.toggle('active');
        menu.classList.toggle('active');
    }

    function toggleSubmenu(event, id) {
        event.preventDefault();
        const submenu = document.getElementById(id + '-submenu');
        submenu.classList.toggle('active');
    }

    document.addEventListener('click', function(event) {
        const menu = document.getElementById('mobileMenu');
        const btn = document.querySelector('.mobile-menu-btn');
        
        if (menu && btn && !menu.contains(event.target) && !btn.contains(event.target)) {
            menu.classList.remove('active');
            btn.classList.remove('active');
        }
    });
</script>