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

        // Get category counts
        $categoryCounts = $this->getCategoryCounts($itemModel);
        
        // Pass data to the view
        $categories = $categoryModel->findAll();
        $organized = $this->organizeCategories($categories, $categoryCounts);

        return view('admin_views/edit_products/edit_products_dash', [
            'products' => $products,
            'categories' => $organized,
            'collections' => $collectionModel->findAll(),
            'dashboard_sections' => $dashData
        ]);
    }

    public function changeVisibility()
    {
        log_message('info', '++++++++++ CHANGE VIS HIT ++++++++++');
        $item_id = $this->request->getPost('item_id');
        log_message('info', 'Item ID: '. $item_id);
        $itemModel = new ItemModel();

        $visibilityStatus = $itemModel
                ->select('visible')
                ->where('item_id', $item_id)
                ->first()['visible'];

        $inverseVis = $visibilityStatus == 1 ? 0 : 1;
        log_message('info', 'Visibility Status: '. $visibilityStatus);
        log_message('info', 'Visibility Status Inverse: '. $inverseVis);
        
        $itemModel
            ->update($item_id, 
                ['visible' => $inverseVis
            ]);

        return redirect()->back();
    }

    private function getCategoryCounts($itemModel) 
    {
        // Get counts for all categories
        $counts = $itemModel
            ->select('category_id, COUNT(*) as item_count')
            ->groupBy('category_id')
            ->findAll();
        
        // Convert to associative array for easy lookup
        $categoryCounts = [];
        foreach ($counts as $count) {
            $categoryCounts[$count['category_id']] = $count['item_count'];
        }
        
        return $categoryCounts;
    }

    private function organizeCategories($categories, $categoryCounts = []) 
    {
        $organized = [];
        
        // First, group parents
        foreach ($categories as $category) {
            if ($category['parent_id'] === null) {
                $organized[$category['category_id']] = [
                    'category' => $category,
                    'children' => [],
                    'item_count' => $categoryCounts[$category['category_id']] ?? 0,
                    'total_count' => 0 // Will be calculated later to include children
                ];
            }
        }
        
        // Then, assign children to their parents
        foreach ($categories as $category) {
            if ($category['parent_id'] !== null) {
                if (isset($organized[$category['parent_id']])) {
                    $childCount = $categoryCounts[$category['category_id']] ?? 0;
                    
                    $organized[$category['parent_id']]['children'][] = array_merge(
                        $category,
                        ['item_count' => $childCount]
                    );
                    
                    // Add child count to parent's total
                    $organized[$category['parent_id']]['total_count'] += $childCount;
                }
            }
        }
        
        // Add parent's own items to total_count
        foreach ($organized as $categoryId => &$parent) {
            $parent['total_count'] += $parent['item_count'];
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
        //log_message('info', '+++++++ Item Id: '. $item_id);
        $itemModel = new ItemModel();
        $itemGeneralInfoModel = new ItemGeneralInfoModel();
        $itemImageModel = new ItemImageModel();
        $categoryModel = new CategoryModel();
        $collectionModel = new CollectionModel();
        $collectionId = $itemModel
                    ->select('collection_id')
                    ->where('item_id',$item_id)
                    ->first()['collection_id'] ?? '';

        //log_message('info','Collection ID: '. $collectionId);

        $category_id = $itemModel
                ->select('category_id')
                ->where('item_id', $item_id)
                ->first()['category_id'];
        //log_message('info', '----- Category Id: '.$category_id);
        
        $item = $itemModel
                ->where('item_id', $item_id)
                ->first();
        
        $generalInfo = $itemGeneralInfoModel
                ->where('item_id', $item_id)
                ->first();
        
        $images = $itemImageModel
            ->where('item_id', $item_id)
            ->orderBy('is_primary', 'DESC')   // primary at the front
            ->orderBy('image_order', 'ASC')   // then by your sort order
            ->orderBy('image_id', 'ASC')      // stable tie-breaker
            ->findAll();


        //log_message('info','((((((((((((( IMAGE INFORMATION ))))))))))))))');
        /* foreach($images as $image){
            log_message('info', 'Image: ' . $image['title']);
        } */
        
        // Get collection info if item belongs to a collection
        $collection = null;
        if (!empty($item['collection_id'])) {
            $collection = $collectionModel
                    ->where('collection_id', $item['collection_id'])
                    ->first();
            //log_message('info', '>>> Collection: ' . ($collection['collection_name'] ?? 'Not found'));
        }
        
        // Get all collections for dropdown (in case user wants to change it)
        $allCollections = $collectionModel->findAll();
        
        $categorySlug = $categoryModel
                ->select('slug')
                ->where('category_id', $category_id)
                ->first()['slug'];
        //log_message('info', '=== Slug: '.$categorySlug);
        
        $parentID = $categoryModel
            ->select('parent_id')
            ->where('category_id', $category_id)
            ->first()['parent_id'];
        
        $parentSlug = $categoryModel
            ->select('slug')
            ->where('category_id', $parentID)
            ->first()['slug'] ?? null;
        //log_message('info', '*** Parent Slug: '.$parentSlug);
        
        switch ($parentSlug) {
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
                $specModel = null;
        }

        //if($specModel) log_message('info', 'Spec Model Found');

        /* $specs = $specModel ? $specModel
                ->where('item_id', $item_id)
                ->first() : null; */
        $specs = $specModel
                ->select()
                ->where('item_id',$item_id)
                ->first();
        /* foreach($specs as $spec){
            log_message('info', 'spec: '.$spec);
        } */

        // test of markigns 
        $markings1 = $markings2 = $markings3 = '';

        if (!empty($generalInfo['markings'])) {
            $markingsParts = explode(',', $generalInfo['markings']);
            $markings1 = trim($markingsParts[0]) ?? '';
            $markings2 = trim($markingsParts[1]) ?? '';
            $markings3 = trim($markingsParts[2]) ?? '';
        }

        /*
        log_message('info','>>>>>>> Markings <<<<<<<<');
        log_message('info','marking 1: '.$markings1);
        log_message('info','marking 2: '.$markings2);
        log_message('info','marking 3: '.$markings3); 
        */

        // test of materials
        $material1 = $material2 = $material3 = '';

        if (!empty($generalInfo['materials'])) {
            $materialsParts = explode(',', $generalInfo['materials']);
            $material1 = trim($materialsParts[0]) ?? '';
            $material2 = trim($materialsParts[1]) ?? '';
            $material3 = trim($materialsParts[2]) ?? '';
        }

        /*         
        log_message('info','>>>>>>> Markings <<<<<<<<');
        log_message('info','materials 1: '.$material1);
        log_message('info','materials 2: '.$material2);
        log_message('info','materials 3: '.$material3); 
        */

        // weight 
        $weightValue = $weightUnit = '';

        if (!empty($generalInfo['weight_label'])) {
            $weightParts = explode(' ', $generalInfo['weight_label']);
            $weightValue = trim($weightParts[0]) ?? '';
            $weightUnit = trim($weightParts[1]) ?? '';
        }
        /* 
        log_message('info','>>>>>>> Weight <<<<<<<<');
        log_message('info','Weight Amount: '.$weightValue);
        log_message('info','Weight Unit: '.$weightUnit);
        */
        // test dimensions
        // format 'string x string x string x unit
        // L,W,H can be blank but unit will default to 
        $dimensionLength = $dimensionWidth = $dimensionHeight = $dimensionUnit = '';

        if (!empty($generalInfo['dimensions_label'])) {
            $dimensionsParts = explode('x', $generalInfo['dimensions_label']);

            // Basic cleanup
            $dimensionLength = isset($dimensionsParts[0]) ? trim($dimensionsParts[0]) : '0';
            $dimensionWidth  = isset($dimensionsParts[1]) ? trim($dimensionsParts[1]) : '0';

            if (isset($dimensionsParts[2])) {
                // Split the last part by space to separate height and unit
                $lastPart = trim($dimensionsParts[2]);
                $heightParts = preg_split('/\s+/', $lastPart); // split by one or more spaces
                $dimensionHeight = $heightParts[0] ?? '0';
                $dimensionUnit   = $heightParts[1] ?? '';       // 'inches', 'cm', etc.
            }
        }
        /*   
        log_message('info','>>>>>>> Dimensions <<<<<<<<');
        log_message('info','Dimensions Length: '.$dimensionLength);
        log_message('info','Dimensions Width: ' .$dimensionWidth);
        log_message('info','Dimensions Height: '.$dimensionHeight);
        log_message('info','Dimensions Unit: '  .$dimensionUnit); 
        */

        /* 
        $isBundle = $collectionModel
                ->select('is_bundle_only')
                ->where('collection_id',$collectionId)
                ->first()['is_bundle_only'] ?? '';
        log_message('info', 'Is it a Bundle'. $isBundle === ''? "True": "False"); 
        */

        $allCategories = (new CategoryModel())->where('is_visible', 1)->findAll();
        $categories = $this->organizeCategories($allCategories);

        return view('admin_views/edit_products/edit_product', [
            'item' => $item,                         // General Info
            'generalInfo' => $generalInfo,           // Historical Info
            'images' => $images,
            'specs' => $specs,
            'categorySlug' => $categorySlug,
            'parentSlug' => $parentSlug,
            'collection' => $collection,              // Current collection (if any)
            'collections' => $allCollections,      // For dropdown to change collection
            'categories' => $categories,            // Organized categories for dropdown
            // return also dimensions, markings, materials vars
            'dimensionLength' => $dimensionLength,
            'dimensionWidth' => $dimensionWidth,
            'dimensionHeight' => $dimensionHeight,
            'dimensionUnit' => $dimensionUnit,

            'markings1' => $markings1,
            'markings2' => $markings2,
            'markings3' => $markings3,

            'material1' => $material1,
            'material2' => $material2,
            'material3' => $material3,
            'weightValue' => $weightValue,
            'weightUnit' => $weightUnit,
        ]);
    }

    public function update_product()
    {
        
    }

    public function update_image()
    {
       
    }

    public function delete_image()
    {

    }

}

