<?php

namespace App\Controllers\admin_controllers;
use App\Models\CategoryModel;
use App\Models\CollectionModel;
use App\Models\ItemBladeSpecsModel;
use App\Models\ItemBookSpecsModel;
use App\Models\ItemDocumentSpecsModel;
use App\Models\ItemFootwearSpecsModel;
use App\Models\ItemGearSpecsModel;
use App\Models\ItemGeneralInfoModel;
use App\Models\ItemHeadgearSpecsModel;
use App\Models\ItemImageModel;
use App\Models\ItemClothInsigniaSpecsModel;
use App\Models\ItemMedalSpecsModel;
use App\Models\ItemModel;
use App\Models\ItemUniformSpecsModel;

class EditProducts_c extends \App\Controllers\BaseController
{
    public function index()
    {
        $dashData = [
            'product_inventory' => [
                'section_title' => 'Product & Inventory',
                'dash_section' => [
                    [
                        'header' => 'Product Management',
                        'p' => 'Manage your product listings, including adding new products, editing existing ones, and removing outdated items.',
                        'links' => [
                            'admin/new-product-details' => 'Add New Product',
                            'admin/edit-products' => 'View All Products',
                        ],
                    ],
                    [
                        'header' => 'Category Management',
                        'p' => 'Create and manage product categories to organize your historical memorabilia collection effectively.',
                        'links' => [
                            'admin/categories/edit' => 'Edit Categories',
                            'admin/categories/new' => 'Add New Category',
                        ],
                    ],
                ],
            ],
            'orders_fulfillment' => [
                'section_title' => 'Orders & Fulfillment',
                'dash_section' => [
                    [
                        'header' => 'Order Management',
                        'p' => 'View and manage customer orders, update order statuses, and handle returns or exchanges.',
                        'links' => [
                            'admin/orders' => 'View Orders',
                            'admin/orders/returns' => 'Manage Returns',
                        ],
                    ],
                ],
            ],
            'customer_relations' => [
                'section_title' => 'Customer Relations',
                'dash_section' => [
                    [
                        'header' => 'Customer Management',
                        'p' => 'Access customer information, manage customer accounts, and view customer activity.',
                        'links' => [
                            'admin/customers' => 'View Customers',
                            'admin/customers/accounts' => 'Manage Accounts',
                        ],
                    ],
                    [
                        'header' => 'Customer Inquiries',
                        'p' => 'Respond to customer inquiries and manage support tickets to ensure customer satisfaction.',
                        'links' => [
                            'admin/inquiries' => 'View Inquiries',
                            'admin/inquiries/tickets' => 'Manage Tickets',
                        ],
                    ],
                ],
            ],
            'analytics_reporting' => [
                'section_title' => 'Analytics & Reporting',
                'dash_section' => [
                    [
                        'header' => 'Reports & Analytics',
                        'p' => 'Generate reports on sales, inventory, and customer behavior to help make informed business decisions.',
                        'links' => [
                            'admin/reports' => 'View Reports',
                            'admin/reports/export' => 'Export Data',
                        ],
                    ],
                ],
            ],
        ];
        // Load necessary models
        $itemModel = new ItemModel();
        $categoryModel = new CategoryModel();
        $collectionModel = new CollectionModel();

        // Fetch all products with their categories and collections
        $products = $itemModel->select('items.*, categories.name as category_name, collections.collection_name as collection_name')
                              ->join('categories', 'items.category_id = categories.category_id', 'left')
                              ->join('collections', 'items.collection_id = collections.collection_id', 'left')
                              ->findAll();

        // Pass data to the view
        $categories = $categoryModel->findAll();
        $organized = $this->organizeCategories($categories);
        /* foreach($organized as $parent_id => $catData){
                log_message('info', 'Parents |  '.$catData['category']['name']);
            foreach($catData['children'] as $child){
                $childName = $child['name'];
                log_message('info', 'Children | '.$childName);
            }
        } */

        return view('admin_views/edit_products/edit_products_dash', [
            'products' => $products,
            'categories' => $organized,
            'collections' => $collectionModel->findAll(),
            'dashboard_sections' => $dashData
        ]);
    }

    private function organizeCategories($categories)
    {
        $organized = [];

        // First, group parents
        foreach ($categories as $category) {
            if ($category['parent_id'] === null) {
                $organized[$category['category_id']] = [
                    'category' => $category,
                    'children' => []
                ];
            }
        }

        // Then, assign children to their parents
        foreach ($categories as $category) {
            if ($category['parent_id'] !== null) {
                if (isset($organized[$category['parent_id']])) {
                    $organized[$category['parent_id']]['children'][] = $category;
                }
            }
        }

        // Sort parent categories by sort_order
        uasort($organized, function ($a, $b) {
            return $a['category']['sort_order'] <=> $b['category']['sort_order'];
        });

        // Sort each parent's children by sort_order
        foreach ($organized as &$parent) {
            if (!empty($parent['children'])) {
                usort($parent['children'], function ($a, $b) {
                    return $a['sort_order'] <=> $b['sort_order'];
                });
            }
        }

        return $organized;
    }

    public function edit_product_category($slug)
    {
        log_message('info', 'Slug: ' . $slug);

        $categoryModel = new CategoryModel();
        $itemModel = new ItemModel();
        $itemGeneralInfo = new ItemGeneralInfoModel();

        // Get the category by slug
        $category = $categoryModel->where('slug', $slug)->first();
        
        if (!$category) {
            // Handle category not found
            return redirect()->back()->with('error', 'Category not found');
        }

        $categoryId = $category['category_id'];
        $categoryName = $category['name'];
        $parentCategoryId = $category['parent_id'];

        log_message('info', 'Category ID: ' . $categoryId);
        log_message('info', 'Parent ID: ' . ($parentCategoryId ?? 'NULL'));

        // Determine category IDs to query
        $categoryIdsToQuery = [];
        
        if ($parentCategoryId === null) {
            // This is a parent category - get all children
            log_message('info', 'Parent category detected - fetching children');
            
            $children = $categoryModel->where('parent_id', $categoryId)->findAll();
            
            // Include parent category itself
            $categoryIdsToQuery[] = $categoryId;
            
            // Add all child category IDs
            foreach ($children as $child) {
                $categoryIdsToQuery[] = $child['category_id'];
            }
            
            // Use parent slug for spec table determination
            $parentCategorySlug = $slug;
        } else {
            // This is a child category - only query this category
            log_message('info', 'Child category detected');
            $categoryIdsToQuery[] = $categoryId;
            
            // Get parent slug for spec table determination
            $parent = $categoryModel->find($parentCategoryId);
            $parentCategorySlug = $parent['slug'] ?? null;
        }

        log_message('info', 'Parent Slug: ' . $parentCategorySlug);
        log_message('info', 'Category IDs to query: ' . implode(', ', $categoryIdsToQuery));

        // Get all items for these categories
        $items = $itemModel
                ->whereIn('category_id', $categoryIdsToQuery)
                ->findAll();
        
        $itemIds = array_column($items, 'item_id');
        
        log_message('info', 'Found ' . count($itemIds) . ' items');

        // Get general info for these items
        $generalInfos = [];
        if (!empty($itemIds)) {
            $generalInfos = $itemGeneralInfo->whereIn('item_id', $itemIds)->findAll();
            
            // Index by item_id for easy lookup
            $generalInfosIndexed = [];
            foreach ($generalInfos as $info) {
                $generalInfosIndexed[$info['item_id']] = $info;
            }
        }

        // Determine which spec model to use based on parent category
        $specModel = null;
        $specs = [];
        
        switch ($parentCategorySlug) {
            case 'blades-edged-weapons':
                $specModel = new ItemBladeSpecsModel();
                break;
            case 'books-manuals':
                $specModel = new ItemBookSpecsModel();
                break;
            case 'documents-paper':
                $specModel = new ItemDocumentSpecsModel();
                break;
            case 'field-gear-accoutrements':
                $specModel = new ItemGearSpecsModel();
                break;
            case 'headgear':
                $specModel = new ItemHeadgearSpecsModel();
                break;
            case 'insignia-awards':
                $specModel = new ItemClothInsigniaSpecsModel();
                break;
            case 'medals':
                $specModel = new ItemMedalSpecsModel();
                break;
            case 'uniforms':
                $specModel = new ItemUniformSpecsModel();
                break;
            default:
                log_message('warning', 'No spec model found for parent category: ' . $parentCategorySlug);
        }

        // Get specs if model exists and we have items
        if ($specModel && !empty($itemIds)) {
            log_message('info', 'Fetching specs from model');
            $specs = $specModel->whereIn('item_id', $itemIds)->findAll();
            
            // Index by item_id for easy lookup
            $specsIndexed = [];
            foreach ($specs as $spec) {
                $specsIndexed[$spec['item_id']] = $spec;
            }
        }

        // Combine all data
        $itemsWithDetails = [];
        foreach ($items as $item) {
            $itemId = $item['item_id'];
            
            $itemsWithDetails[] = [
                'item' => $item,
                'general_info' => $generalInfosIndexed[$itemId] ?? null,
                'specs' => $specsIndexed[$itemId] ?? null,
            ];
        }

        log_message('info', 'Prepared ' . count($itemsWithDetails) . ' items with details');

        // Pass to view
        return view('admin_views/edit_products/edit_category', [
            'category' => $category,
            'parent_slug' => $parentCategorySlug,
            'items_with_details' => $itemsWithDetails,
            'is_parent_category' => $parentCategoryId === null,
            'categoryName' => $categoryName,
        ]);
    }

    public function edit_product($item_id)
    {
        // given the item id get all information for item from all relevant tables (items, itemGeneralInfo, spec table if applicable)
        return view('admin_views/edit_products/edit_product');
    }
    
}

