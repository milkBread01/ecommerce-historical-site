<?php

namespace App\Controllers\customer_controllers;
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

class CustomerController_c extends \App\Controllers\BaseController
{

    public function index($categorySlug)
    {
        log_message('debug', 'Cat Slug: ' . $categorySlug);

        $categoryModel       = new CategoryModel();
        $itemModel           = new ItemModel();
        $itemHistoricalInfo  = new ItemGeneralInfoModel();
        $imageModel          = new ItemImageModel();

        // Current category
        $categoryRow = $categoryModel
            ->where('slug', $categorySlug)
            ->first();

        if (!$categoryRow) {
            // handle 404 or redirect as you prefer
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Category not found");
        }

        $categoryId = $categoryRow['category_id'];
        log_message('debug', 'cat Id: ' . $categoryId);

        // Determine if this category is a parent (no parent_id) or a child
        $isParent = empty($categoryRow['parent_id']);

        // Build categories for linkage + allowed IDs for items
        if ($isParent) {
            // Linkage: parent + its children
            $categoriesForLinks = $categoryModel
                ->where('is_visible', 1)
                ->groupStart()
                    ->where('parent_id', $categoryId)     // children
                    ->orWhere('category_id', $categoryId) // the parent
                ->groupEnd()
                ->orderBy('sort_order', 'ASC')
                ->findAll();

            // Items: parent + children
            $allowedCatIds = array_column($categoriesForLinks, 'category_id');
        } else {
            $parentId = $categoryRow['parent_id'];

            // Linkage: parent + all its children (for navigation/siblings)
            $categoriesForLinks = $categoryModel
                ->where('is_visible', 1)
                ->groupStart()
                    ->where('parent_id', $parentId)       // all siblings
                    ->orWhere('category_id', $parentId)   // the parent
                ->groupEnd()
                ->orderBy('sort_order', 'ASC')
                ->findAll();

            // Items: ONLY this child
            $allowedCatIds = [$categoryId];
        }


        // Items in allowed categories
        $allowedItems = [];
        if (!empty($allowedCatIds)) {
            $allowedItems = $itemModel
                ->whereIn('category_id', $allowedCatIds)
                ->where('visible',1)
                ->findAll();
        }

        // If there are no items, prep empty structures and render
        if (empty($allowedItems)) {
            $data = [
                'category'       => $categoryRow,
                'categories'     => $categoriesForLinks,      // full category rows (name, slug, etc.)
                'categoryIds'    => $allowedCatIds,   // flat array of IDs
                'items'          => [],               // no items
                'countryByItem'  => [],               // item_id => country_origin
                'imageByItem'    => [],               // item_id => primary image path
            ];
            return view('customer_views/category_view', $data);
        }

        // Collect item IDs
        $itemIds = array_column($allowedItems, 'item_id');

        // Primary images (one query)
        $images = $imageModel
            ->select('item_id, file_path')
            ->whereIn('item_id', $itemIds)
            ->where('is_primary', 1)
            ->findAll();

        $imageByItem = [];
        foreach ($images as $img) {
            $imageByItem[$img['item_id']] = $img['file_path'];
        }

        // Historical info (one query)
        $hist = $itemHistoricalInfo
            ->select('item_id, country_origin')
            ->whereIn('item_id', $itemIds)
            ->findAll();

        $histByItem = [];
        foreach ($hist as $h) {
            $histByItem[$h['item_id']] = $h['country_origin'];
        }

        // Enrich items with primary image + country_origin
        $enrichedItems = [];
        foreach ($allowedItems as $it) {
            $id = $it['item_id'];
            $it['primary_image_path'] = $imageByItem[$id] ?? null;
            $it['country_origin']     = $histByItem[$id]  ?? null;
            $enrichedItems[] = $it;
        }

        // Build data payload for the view
        $data = [
            'category'       => $categoryRow,        // current category row
            'categories'     => $categoriesForLinks, // linkage list (parent + children)
            'categoryIds'    => $allowedCatIds,      // item filter IDs (child-only OR parent+children)
            'items'          => $enrichedItems,      // enriched items
            'countryByItem'  => $histByItem,         // map: item_id => country_origin
            'imageByItem'    => $imageByItem,        // map: item_id => primary_image_path
        ];

        return view('customer_views/category_view', $data);
    }
   
}