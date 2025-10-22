<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Navigation extends BaseController
{
    /**
     * Get navigation data for header
     * This can be called as a helper method or used in a filter
     */
    public function getNavigationData(): array
    {
        $categoryModel = new CategoryModel();
        
        // Get all visible parent categories (where parent_id is NULL)
        $parentCategories = $categoryModel
            ->where('is_visible', 1)
            ->where('parent_id', null)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        // Build navigation structure with children
        $navigationData = [];
        
        foreach ($parentCategories as $parent) {
            $children = $categoryModel->getChildren($parent['category_id'], true);
            
            $navigationData[] = [
                'category_id' => $parent['category_id'],
                'name'        => $parent['name'],
                'slug'        => $parent['slug'],
                'sort_order'  => $parent['sort_order'],
                'children'    => $children
            ];
        }

        return $navigationData;
    }

    /**
     * AJAX endpoint to get navigation data as JSON
     * Useful for dynamic loading or SPAs
     */
    public function getNavigationJson()
    {
        $data = $this->getNavigationData();
        return $this->response->setJSON($data);
    }
}