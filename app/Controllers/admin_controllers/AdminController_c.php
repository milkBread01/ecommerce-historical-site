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

class AdminController_c extends \App\Controllers\BaseController
{
    public function index()
    {
        return view('admin_views/admin_dash');
    }

    public function new_product_details()
    {
        $allCategories = (new CategoryModel())->where('is_visible', 1)->findAll();
        $categories = $this->organizeCategories($allCategories);

        // Check if redirecting from new collection creation
        $preSelectedCollectionId = $this->request->getGet('collection_id');
        $isNewCollection = $this->request->getGet('new_collection') === '1';

        $dimensionLength = $dimensionWidth = $dimensionHeight = $dimensionUnit = '';
        $weightValue = $weightUnit = '';
        $material1 = $material2 = $material3 = '';
        $markings1 = $markings2 = $markings3 = '';

        $data = [
            'categories' => $categories,
            'itemInformation'  => (new ItemModel())->findAll(),
            'itemGeneralSpecs' => (new ItemGeneralInfoModel())->findAll(),
            'collections'      => (new CollectionModel())->findAll(),
            'preSelectedCollectionId' => $preSelectedCollectionId,
            'isNewCollection' => $isNewCollection,
            
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
        ];
        return view('admin_views/product_creation/new_product_details', $data);
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

    public function new_product_images()
    {
        $itemId = (int)$this->request->getGet('item_id');
        
        $data = [
            'item_id' => $itemId
        ];
        
        return view('admin_views/product_creation/new_product_images', $data);
    }

    /* details form validation */
    public function validate_npf_details()
    {
        log_message('info', '==== validate_npf_details() STARTED ====');
        
        $entryType = $this->request->getPost('entry_type');
        log_message('info', 'Entry Type: ' . ($entryType ?? 'NULL'));

        // ---- HANDLE COLLECTION CREATION ----
        if ($entryType === 'create_collection') {
            log_message('info', 'Processing collection creation...');
            
            $rules = [
                'collection_name' => 'required|min_length[3]|max_length[255]|is_unique[collections.collection_name]',
                'collection_description' => 'required|min_length[10]',
                'is_bundle_only' => 'required|in_list[0,1]',
            ];
            
            if (!$this->validate($rules)) {
                log_message('error', 'Collection validation FAILED: ' . json_encode($this->validator->getErrors()));
                return redirect()->back()->withInput()->with('validation', $this->validator);
            }
            
            log_message('info', 'Collection validation passed');
            
            $collectionData = [
                'collection_name' => $this->request->getPost('collection_name'),
                'collection_slug' => url_title($this->request->getPost('collection_name')),
                'collection_description' => $this->request->getPost('collection_description'),
                'is_bundle_only' => $this->request->getPost('is_bundle_only'),
                'bundle_price' => $this->request->getPost('collection_price') ?: null,
            ];
            
            log_message('info', 'Collection data prepared: ' . json_encode($collectionData));
            
            $collectionId = (new CollectionModel())->insert($collectionData);
            
            if (!$collectionId) {
                log_message('error', 'Collection insert FAILED');
                return redirect()->back()->withInput()->with('error', 'Failed to create collection.');
            }
            
            log_message('info', 'Collection created successfully with ID: ' . $collectionId);
            
            // Store collection info in session for later use
            session()->set('new_collection_id', $collectionId);
            session()->set('new_collection_name', $collectionData['collection_name']);
            
            // Continue processing as if it were an individual item creation
            // The collection is already created, now we create the first item
            log_message('info', 'Collection created with ID: ' . $collectionId . '. Continuing to create first item.');
        }

        // ---- HANDLE INDIVIDUAL ITEM (NEW OR ASSIGNED TO COLLECTION) ----
        log_message('info', 'Processing item creation...');
        
        // Load models
        $itemGeneral   = new ItemGeneralInfoModel();
        $itemModel     = new ItemModel();
        $categoryModel = new CategoryModel();

        $post = $this->request->getPost();
        log_message('info', 'Raw POST data keys: ' . implode(', ', array_keys($post)));

        // Normalize booleans
        $post['on_sale']         = isset($post['on_sale']) ? 1 : 0;
        $post['is_featured']     = isset($post['is_featured']) ? 1 : 0;
        $post['visible']         = isset($post['visible']) ? 1 : 0;
        $post['documentation']   = isset($post['documentation']) ? 1 : 0;
        $post['certificate_auth']= isset($post['certificate_auth']) ? 1 : 0;

        log_message('info', 'Booleans normalized');

        // Validate item data
        $rules = $itemModel->getValidationRules() ?? [];
        
        // ✅ Make collection_id optional for individual items
        if (isset($rules['collection_id'])) {
            $rules['collection_id'] = 'permit_empty|is_natural_no_zero';
        }
        
        log_message('info', 'Item validation rules count: ' . count($rules));
        log_message('info', 'Modified collection_id rule: ' . ($rules['collection_id'] ?? 'not set'));

        if (!$this->validate($rules)) {
            log_message('error', 'Item validation FAILED: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        log_message('info', 'Item validation passed');

        // Category validation
        $categoryId = (int)($post['category_id'] ?? 0);
        log_message('info', 'Category ID: ' . $categoryId);
        
        if (!$categoryId || !$categoryModel->find($categoryId)) {
            log_message('error', 'Category validation FAILED - Category ID: ' . $categoryId);
            return redirect()->back()->withInput()->with('error', 'Category is required and must exist.');
        }

        log_message('info', 'Category validation passed');

        // Process discount
        $discountedPrice = null;
        $price = (float)($post['price'] ?? 0);
        
        if (!empty($post['amount_discount'])) {
            $discountedPrice = (float)$post['amount_discount'];
            log_message('info', 'Amount discount applied: ' . $discountedPrice);
        } elseif (!empty($post['percentage_discount']) && $post['on_sale']) {
            $percentageOff = (float)$post['percentage_discount'];
            $discountedPrice = $price - ($price * ($percentageOff / 100));
            log_message('info', 'Percentage discount applied: ' . $percentageOff . '% = ' . $discountedPrice);
        }

        // Process materials
        $materials = array_filter([
            trim($post['material_1'] ?? ''),
            trim($post['material_2'] ?? ''),
            trim($post['material_3'] ?? '')
        ], function($val) { 
            // Filter out empty strings, null, and "[object Object]"
            return $val !== '' && $val !== null && !str_contains($val, '[object'); 
        });
        $materialsStr = !empty($materials) ? implode(', ', $materials) : null;
        log_message('info', 'Materials processed: ' . ($materialsStr ?? 'NULL'));

        // Process markings
        $markings = array_filter([
            trim($post['marking_1'] ?? ''),
            trim($post['marking_2'] ?? ''),
            trim($post['marking_3'] ?? '')
        ], function($val) { 
            // Filter out empty strings, null, and "[object Object]"
            return $val !== '' && $val !== null && !str_contains($val, '[object'); 
        });
        $markingsStr = !empty($markings) ? implode(', ', $markings) : null;
        log_message('info', 'Markings processed: ' . ($markingsStr ?? 'NULL'));

        // Process dimensions
        $dimensionsStr = null;
        if (!empty($post['dimension_length']) && !empty($post['dimension_width']) && !empty($post['dimension_height'])) {
            $dimensionsStr = sprintf(
                "%s x %s x %s %s",
                $post['dimension_length'],
                $post['dimension_width'],
                $post['dimension_height'],
                $post['dimension_unit'] ?? 'cm'
            );
            log_message('info', 'Dimensions processed: ' . $dimensionsStr);
        }

        // Process weight
        $weightStr = null;
        if (!empty($post['weight_value'])) {
            $weightStr = sprintf(
                "%s %s",
                $post['weight_value'],
                $post['weight_unit'] ?? 'kg'
            );
            log_message('info', 'Weight processed: ' . $weightStr);
        }

        // Determine collection_id: prioritize newly created collection from session
        $collectionId = session()->get('new_collection_id') 
                        ?: ($this->request->getPost('collection_id') ?: null);
        
        log_message('info', 'Final collection_id: ' . ($collectionId ?? 'NULL'));

        // Build item payload
        $itemData = [
            'category_id'     => $categoryId,
            'collection_id'   => $collectionId,

            'sku'             => $post['sku'] ?? null,
            'name'            => $post['name'] ?? null,
            'slug'            => url_title($post['name'] ?? '', '-', true),
            'description'     => $post['description'] ?? null,
            'teaser'          => $post['teaser'] ?? null,
            'video_url'       => $post['video_url'] ?? null,
            'price'           => $price,
            'discounted_price'=> $discountedPrice,
            'on_sale'         => $post['on_sale'],
            'is_featured'     => $post['is_featured'],
            'visible'         => $post['visible'],
            'stock_quantity'  => (int)($post['stock_quantity'] ?? 0),
        ];

        log_message('info', 'Item data prepared: ' . json_encode($itemData));

        // Build general info payload
        $generalData = [
            'era_period'        => $post['era_period'] ?? null,
            'country_origin'    => $post['country_origin'] ?? null,
            'branch_org'        => $post['branch_org'] ?? null,
            'unit_regiment'     => $post['unit_regiment'] ?? null,
            'authenticity'      => !empty($post['authenticity']) ? trim($post['authenticity']) : null,
            'condition'         => !empty($post['condition']) ? trim($post['condition']) : null,
            'dimensions_label'  => $dimensionsStr,
            'weight_label'      => $weightStr,
            'materials'         => $materialsStr,
            'markings'          => $markingsStr,
            'serial_numbers'    => $post['serial_numbers'] ?? null,
            'provenance_source' => $post['provenance_source'] ?? null,
            'documentation'     => $post['documentation'],
            'documentation_type'=> $post['documentation_type'] ?? null,
            'certificate_auth'  => $post['certificate_auth'],
            'certificate_type'  => $post['certificate_type'] ?? null,
        ];

        log_message('info', 'General data prepared (without item_id): ' . json_encode($generalData));

        // Transaction: insert both or rollback
        $db = \Config\Database::connect();
        $db->transStart();
        
        log_message('info', 'Database transaction started');

        if (!$itemModel->insert($itemData)) {
            $db->transRollback();
            log_message('error', 'Item insert FAILED: ' . json_encode($itemModel->errors() ?: []));
            return redirect()->back()->withInput()->with('error', 
                'Could not save item: ' . implode('; ', $itemModel->errors() ?: [])
            );
        }
        
        log_message('info', 'Item inserted successfully');
        
        $itemId = $itemModel->getInsertID();
        if (!$itemId) {
            $db->transRollback();
            log_message('error', 'Could not retrieve item ID after insert');
            return redirect()->back()->withInput()->with('error', 'Could not retrieve inserted item ID');
        }

        log_message('info', 'Item ID retrieved: ' . $itemId);

        $generalData['item_id'] = (int)$itemId;

        log_message('info', 'Attempting to insert general info with data: ' . json_encode($generalData));

        if (!$itemGeneral->insert($generalData)) {
            $db->transRollback();
            $errors = $itemGeneral->errors() ?: [];
            $dbError = $db->error();
            
            log_message('error', '==== ItemGeneralInfo Insert FAILED ====');
            log_message('error', 'Item ID: ' . $itemId);
            log_message('error', 'Data: ' . json_encode($generalData));
            log_message('error', 'Model Errors: ' . json_encode($errors));
            log_message('error', 'DB Error Code: ' . ($dbError['code'] ?? 'unknown'));
            log_message('error', 'DB Error Message: ' . ($dbError['message'] ?? 'unknown'));
            log_message('error', '====================================');
            
            $errorMsg = !empty($errors) 
                ? implode('; ', $errors)
                : ($dbError['message'] ?? 'Database error - check writable/logs/');
            
            return redirect()->back()->withInput()->with('error', 
                'Could not save general info: ' . $errorMsg
            );
        }
        
        log_message('info', 'Successfully inserted general info for item_id: ' . $itemId);

        $db->transComplete();
        if (!$db->transStatus()) {
            log_message('error', 'Transaction FAILED to complete');
            return redirect()->back()->withInput()->with('error', 'Transaction failed while saving product.');
        }

        log_message('info', 'Database transaction completed successfully');

        // Get parent slug and send to savePartials
        $parentID = $categoryModel
            ->select('parent_id')
            ->where('category_id', $categoryId)
            ->first()['parent_id'] ?? null;
        if($parentID == null) $parentID = $categoryId;

        log_message('info', 'Parent category ID determined: ' . $parentID);

        $parentSlug = $categoryModel
            ->select('slug')
            ->where('category_id', $parentID)
            ->first()['slug'] ?? null;

        log_message('info', 'Parent slug retrieved: ' . ($parentSlug ?? 'NULL'));
        log_message('info', 'Calling savePartials with slug: ' . $parentSlug . ', item_id: ' . $itemId);

        $partialSave = $this->savePartials($parentSlug, (int)$itemId);
        if ($partialSave instanceof \CodeIgniter\HTTP\RedirectResponse) {
            log_message('error', 'savePartials returned a redirect (error occurred)');
            return $partialSave; // propagate redirect on error
        }

        log_message('info', 'savePartials completed successfully');

        // Build success message
        $successMessage = 'Product details saved. You can now upload images.';
        $newCollectionName = session()->get('new_collection_name');
        
        if ($newCollectionName) {
            $successMessage = "Collection '{$newCollectionName}' created and first item added! You can now upload images, then add more items to this collection.";
            // Clear the session data after use
            session()->remove('new_collection_id');
            session()->remove('new_collection_name');
            log_message('info', 'Collection creation flow completed: ' . $newCollectionName);
        } elseif ($collectionId) {
            $collectionModel = new CollectionModel();
            $collection = $collectionModel->find($collectionId);
            if ($collection) {
                $successMessage = "Item added to collection '{$collection['collection_name']}'! You can now upload images.";
                log_message('info', 'Item added to existing collection: ' . $collection['collection_name']);
            }
        }

        log_message('info', 'Success message prepared: ' . $successMessage);
        log_message('info', 'Redirecting to image upload page for item_id: ' . $itemId);
        log_message('info', '==== validate_npf_details() COMPLETED SUCCESSFULLY ====');

        // Always redirect to image upload page
        return redirect()
            ->to(site_url('admin/new-product-images?item_id=' . $itemId))
            ->with('success', $successMessage);
    }

    private function savePartials($slug, $itemId)
    {
        if (!$slug || !$itemId) {
            return null; // nothing to save
        }
        log_message('info', 'Saving partial specs for category slug: ' . $slug . ' and item_id: ' . $itemId);
        $post = $this->request->getPost();

        // depending on slug, call the appropriate model and save data, item_id is the foreign key
        switch($slug){
            case 'blades-edged-weapons':
                $model = new ItemBladeSpecsModel();
                $rules = $model->getValidationRules() ?? [];
                log_message('info', 'Saving blade specs for item_id: ' . $itemId);
                break;
            case 'books-manuals':
                $model = new ItemBookSpecsModel();
                $rules = $model->getValidationRules() ?? [];
                log_message('info', 'Saving book specs for item_id: ' . $itemId);
                break;
            case 'documents-paper':
                $model = new ItemDocumentSpecsModel();
                $rules = $model->getValidationRules() ?? [];
                log_message('info', 'Saving document specs for item_id: ' . $itemId);
                break;
            /* case 'footwear':
                $model = new ItemFootwearSpecsModel();
                $rules = $model->getValidationRules() ?? [];
                break; */
            case 'field-gear-accoutrements':
                $model = new ItemGearSpecsModel();
                $rules = $model->getValidationRules() ?? [];
                log_message('info', 'Saving gear specs for item_id: ' . $itemId);
                break;
            case 'headgear':
                $model = new ItemHeadgearSpecsModel();
                $rules = $model->getValidationRules() ?? [];
                log_message('info', 'Saving headgear specs for item_id: ' . $itemId);
                break;
            case 'insignia-awards':
                $model = new ItemClothInsigniaSpecsModel();
                $rules = $model->getValidationRules() ?? [];
                log_message('info', 'Saving cloth insignia specs for item_id: ' . $itemId);
                break;
            case 'medals':
                $model = new ItemMedalSpecsModel();
                $rules = $model->getValidationRules() ?? [];
                log_message('info', 'Saving medal specs for item_id: ' . $itemId);
                break;
            case 'uniforms':
                $model = new ItemUniformSpecsModel();
                $rules = $model->getValidationRules() ?? [];
                log_message('info', 'Saving uniform specs for item_id: ' . $itemId);
                break;
            default:
                return; // no additional specs to save
        }

        // Prepare data for insertion
        $specData = ['item_id' => $itemId];
        
        // Get the POST data and filter it based on what's actually submitted
        foreach ($post as $key => $value) {
            // Skip CSRF token and item_id (already set)
            if ($key === 'csrf_test_name' || $key === 'item_id') {
                continue;
            }
            
            // Skip fields from the main form that aren't part of specs
            $mainFormFields = ['entry_type', 'collection_id', 'collection_name', 'collection_description',
                          'is_bundle_only', 'collection_price', 'category_id', 'sku', 'name', 
                          'description', 'teaser', 'video_url', 'price', 'amount_discount', 
                          'percentage_discount', 'on_sale', 'is_featured', 'visible', 'stock_quantity',
                          'era_period', 'country_origin', 'branch_org', 'unit_regiment', 'authenticity',
                          'condition', 'dimension_length', 'dimension_width', 'dimension_height',
                          'dimension_unit', 'weight_value', 'weight_unit', 'material_1', 'material_2',
                          'material_3', 'marking_1', 'marking_2', 'marking_3', 'serial_numbers',
                          'provenance_source', 'documentation', 'documentation_type', 'certificate_auth',
                          'certificate_type', 'dimensions_label', 'weight_label', 'materials', 'markings'];
            
            if (in_array($key, $mainFormFields)) {
                continue;
            }
            
            // Add non-empty values to spec data
            if ($value !== null && $value !== '') {
                // Handle boolean fields specifically
                if (in_array($key, ['rank_insignia_present', 'unit_patches_present', 'buttons_original', 'scabbard_included', 'signature_present', 'stamp_seal_present', 'straps_attachments_pres', 'insignia_present', 'chinstrap_present', 'ribbon_present', 'presentation_case_incl'])) {
                    $specData[$key] = (int)$value;
                } else {
                    $specData[$key] = $value;
                }
            }
        }

        // Log the data being validated/inserted
        log_message('info', 'Spec data for validation: ' . json_encode($specData));

        // Validate using the model's validation - pass false to skip validation in insert
        // and do it manually first so we can log errors properly
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        
        if (!$validation->run($specData)) {
            $validationErrors = $validation->getErrors();
            log_message('error', 'Validation failed for item specs for item_id: ' . $itemId);
            log_message('error', 'Validation errors: ' . json_encode($validationErrors));
            log_message('error', 'Spec data sent: ' . json_encode($specData));
            return redirect()->back()->withInput()->with('errors', $validationErrors);
        }
        
        // Insert with validation disabled since we already validated
        if (!$model->insert($specData, false)) {
            $modelErrors = $model->errors() ?: [];
            log_message('error', 'Failed to save item specs for item_id: ' . $itemId);
            log_message('error', 'Model errors: ' . json_encode($modelErrors));
            return redirect()->back()->withInput()->with('error', 
                'Could not save item specs: ' . implode('; ', $modelErrors)
            );
        }
        log_message('info', 'Successfully saved item specs for item_id: ' . $itemId);

        return true;
    }

    public function productFields()
    {
        $categoryModel = new CategoryModel();
        $category_id = $this->request->getPost('category_id');
        
        // Get item_id from POST (we'll add this to the AJAX call)
        $item_id = $this->request->getPost('item_id');
        if(!$item_id) log_message('info', 'No Item Id (creating new item)');
        
        $slug = $categoryModel->find($category_id)['slug'] ?? null;

        $parentID = $categoryModel
            ->select('parent_id')
            ->where('slug', $slug)
            ->first()['parent_id'] ?? null;

        if($parentID == null) $parentID = $category_id;

        $parentSlug = $categoryModel
            ->select('slug')
            ->where('category_id', $parentID)
            ->first()['slug'] ?? null;

        /* log_message('info', '==================== Item ID: '. $item_id);
        log_message('info', 'Received AJAX request for category_id: ' . $category_id);
        log_message('info', 'slug: ' . $slug);
        log_message('info', 'parent ID: ' . $parentID);
        log_message('info', 'parent slug: ' . $parentSlug); */

        // Load specs if we have an item_id (editing mode)
        $specs = null;
        if ($item_id) {
            log_message('info', 'Loading specs for item_id: ' . $item_id);
            
            // Determine which spec model to use
            $specModel = null;
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
            }

            // Get specs if model exists
            if ($specModel) {
                $specs = $specModel->where('item_id', $item_id)->first();
                log_message('info', 'Specs loaded: ' . ($specs ? 'Yes' : 'No'));
            }
        }

        $data = [
            'itemInformation'  => (new ItemModel())->findAll(),
            'itemGeneralSpecs' => (new ItemGeneralInfoModel())->findAll(),
            'specs' => $specs,  // ✅ Pass specs to the partial
        ];

        try{
            return view('admin_views/product_creation/partials/' . $parentSlug, $data);
        } catch(\Exception $e) {
            log_message('error', 'Error loading partial: ' . $e->getMessage());
            return null;
        }
    }

    public function validate_npf_images()
    {
        log_message('info', 'Starting image upload process.');
        
        // get item id from hidden input field
        $itemId = (int)$this->request->getPost('item_id');
        log_message('info', $itemId ? 'Item ID: ' . $itemId : 'No Item ID provided in request.');
        if (!$itemId) {
            log_message('error', 'Item ID is required to upload images.');
            return redirect()->back()->withInput()->with('error', 'Item ID is required to upload images.');
        }

        // ensure item id exists in items table
        $itemModel = new ItemModel();
        $item = $itemModel->find($itemId);
        if (!$item) {
            log_message('error', 'Item not found for the given ID: ' . $itemId);
            return redirect()->back()->withInput()->with('error', 'Item not found for the given ID.');
        }

        $imageModel = new ItemImageModel();

        // Get form data
        $titles = $this->request->getPost('title') ?? [];
        $descriptions = $this->request->getPost('description') ?? [];
        $altTexts = $this->request->getPost('alt_text') ?? [];
        $removeFlags = $this->request->getPost('remove') ?? [];
        $imageOrders = $this->request->getPost('image_order') ?? [];
        $existingImageIds = $this->request->getPost('existing_image_id') ?? [];

        log_message('info', 'Form data - Titles: ' . count($titles) . ', Existing IDs: ' . count($existingImageIds));
        log_message('info', 'Existing image indices: ' . json_encode(array_keys($existingImageIds)));
        log_message('info', 'Image orders: ' . json_encode($imageOrders));

        // get uploaded files
        $files = $this->request->getFiles();
        $uploadedFiles = $files['product_images'] ?? [];
        
        // Check if there are any new uploads or existing images to update
        $hasNewUploads = !empty($uploadedFiles) && is_array($uploadedFiles);
        $hasExistingImages = !empty($existingImageIds);
        
        if (!$hasNewUploads && !$hasExistingImages) {
            log_message('info', 'No new uploads and no existing images to update - allowing save');
        }

        if (!is_array($uploadedFiles)) {
            $uploadedFiles = [$uploadedFiles];
        }

        // Determine storage path based on category parent slug
        $categoryModel = new CategoryModel();
        $category = $categoryModel->find($item['category_id']);
        $parentSlug = null;

        if ($category) {
            $parentID = $category['parent_id'] ?? null;
            if ($parentID === null) {
                $parentID = $category['category_id'];
            }
            $parentCategory = $categoryModel->find($parentID);
            $parentSlug = $parentCategory['slug'] ?? null;
        }

        $imageBasePath = 'assets/images/product_images/' . $parentSlug . '/' . $itemId . '/';
        $targetDir = FCPATH . $imageBasePath;
        
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        log_message('info', 'Uploading images for item_id: ' . $itemId . ' under path: ' . $imageBasePath);

        $errors = [];
        $db = \Config\Database::connect();
        $db->transStart();

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $dbPrimaryImageId = $imageModel
                    ->select('image_id')
                    ->where('is_primary', 1)
                    ->where('item_id', $itemId)
                    ->first()['image_id'] ?? null;
        
        $orderdImagesCopy = [];
        foreach($existingImageIds as $idx => $imageId){
            log_message('info', "Index {$idx}: existing_image_id {$imageId}");
            $orderdImagesCopy[] = $imageId;
        }

        // STEP 1: Handle existing images (updates and deletions)
        if ($hasExistingImages) {
            log_message('info', 'Processing ' . count($existingImageIds) . ' existing images');

            $currentPrimaryImage = $orderdImagesCopy[0] ?? null;
            if($currentPrimaryImage && $dbPrimaryImageId && $dbPrimaryImageId !== $currentPrimaryImage){
                log_message('info', "Changing primary image from {$dbPrimaryImageId} to {$currentPrimaryImage}");
                // setting old primary to 0
                $imageModel->update($dbPrimaryImageId, ['is_primary' => 0]);
                // setting new primary to 1
                $imageModel->update($currentPrimaryImage, ['is_primary' => 1]);
            }
            
            // Collect all existing image IDs and their new orders
            $imagesToUpdate = [];
            $imagesToDelete = [];
            
            foreach ($existingImageIds as $index => $imageId) {
                $imageId = (int)$imageId;
                if ($imageId <= 0) continue;
                
                $removeFlag = $removeFlags[$index] ?? '0';
                
                if ($removeFlag === '1') {
                    $imagesToDelete[] = $imageId;
                    log_message('info', "Image ID {$imageId} marked for deletion");
                } else {
                    $imagesToUpdate[$imageId] = [
                        'index' => $index,
                        'title' => $titles[$index] ?? '',
                        'description' => $descriptions[$index] ?? null,
                        'alt_text' => $altTexts[$index] ?? null,
                        'image_order' => (int)($imageOrders[$index] ?? $index),
                    ];
                    log_message('info', "Image ID {$imageId} will be updated with order " . $imagesToUpdate[$imageId]['image_order']);
                }
            }
            
            // Delete marked images
            foreach ($imagesToDelete as $imageId) {
                log_message('info', 'Deleting image ID: ' . $imageId);
                $existingImage = $imageModel->find($imageId);
                if ($existingImage) {
                    $filePath = FCPATH . $existingImage['file_path'];
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                    $imageModel->delete($imageId);
                    log_message('info', 'Deleted image ID: ' . $imageId);
                }
            }
            
            // Update images with new order - use a high offset to avoid conflicts
            // First, find the maximum order value we're about to set
            $maxNewOrder = 0;
            foreach ($imagesToUpdate as $data) {
                if ($data['image_order'] > $maxNewOrder) {
                    $maxNewOrder = $data['image_order'];
                }
            }
            
            // Use an offset higher than any order we'll set
            $tempOffset = $maxNewOrder + 1000;
            
            log_message('info', "Using temporary offset of {$tempOffset} to avoid order conflicts");
            
            // Temporarily set all images to offset values
            foreach ($imagesToUpdate as $imageId => $data) {
                $tempOrder = $tempOffset + $imageId;
                log_message('info', "Setting image {$imageId} to temporary order {$tempOrder}");
                
                // Skip validation for this temporary update
                $imageModel->skipValidation(true);
                $imageModel->update($imageId, ['image_order' => $tempOrder]);
                $imageModel->skipValidation(false);
            }
            
            // Now update with actual values
            foreach ($imagesToUpdate as $imageId => $data) {
                $updateData = [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'alt_text' => $data['alt_text'],
                    'image_order' => $data['image_order'],
                ];

                log_message('info', "Updating image {$imageId} with final data: " . json_encode($updateData));

                if (!$imageModel->update($imageId, $updateData)) {
                    $modelErrors = $imageModel->errors();
                    $errorMsg = "Failed to update Image ID: {$imageId}: " . json_encode($modelErrors);
                    $errors[] = $errorMsg;
                    log_message('error', $errorMsg);
                } else {
                    log_message('info', 'Successfully updated image ID: ' . $imageId . ' with order: ' . $updateData['image_order']);
                }
            }
        }
        
        // STEP 2: Handle new uploads
        if ($hasNewUploads) {
            log_message('info', 'Processing new file uploads - total files: ' . count($uploadedFiles));
            
            // Check if a primary image already exists for this item
            $existingPrimary = $imageModel->where('item_id', $itemId)
                                ->where('is_primary', 1)
                                ->first();
            log_message('info', 'Existing primary image for item_id ' . $itemId . ': ' . ($existingPrimary ? 'yes (ID: ' . $existingPrimary['image_id'] . ')' : 'no'));

            $hasPrimaryImage = $existingPrimary !== null;
            
            // Get the highest existing order to append new images
            $maxOrder = $imageModel->where('item_id', $itemId)
                                ->selectMax('image_order')
                                ->first()['image_order'] ?? -1;
            
            log_message('info', "Current max order in DB: {$maxOrder}");

            // Create a map of which indices have existing_image_ids (those are NOT new uploads)
            $indicesWithExistingIds = array_keys($existingImageIds);
            log_message('info', 'Indices with existing IDs: ' . json_encode($indicesWithExistingIds));
            
            // Find all indices that DON'T have existing_image_ids (those ARE new uploads)
            $newUploadIndices = [];
            foreach ($titles as $idx => $title) {
                if (!isset($existingImageIds[$idx]) || empty($existingImageIds[$idx])) {
                    $newUploadIndices[] = $idx;
                    log_message('info', "Index {$idx} identified as NEW upload (title: {$title})");
                }
            }
            log_message('info', 'New upload indices: ' . json_encode($newUploadIndices));

            if (count($newUploadIndices) !== count($uploadedFiles)) {
                log_message('warning', 'Mismatch: ' . count($uploadedFiles) . ' files uploaded but ' . count($newUploadIndices) . ' new indices found');
            }

            // Now process uploaded files and match them to their correct indices
            $fileIndex = 0;
            foreach ($uploadedFiles as $file) {
                // Skip empty file inputs (error code 4 = UPLOAD_ERR_NO_FILE)
                if ($file->getError() === 4) {
                    log_message('info', "Skipping empty file input (no file selected)");
                    continue;
                }
                
                // Skip if we've run out of new upload indices
                if ($fileIndex >= count($newUploadIndices)) {
                    log_message('warning', 'More files uploaded than expected new indices');
                    break;
                }
                
                // Get the actual form index for this file
                $formIndex = $newUploadIndices[$fileIndex];
                $fileIndex++;
                
                log_message('info', "Processing file #{$fileIndex} ('{$file->getClientName()}') for form index: {$formIndex}");

                if (!$file->isValid()) {
                    $error = $file->getErrorString() . ' (' . $file->getError() . ')';
                    log_message('error', "File at form index {$formIndex} is invalid: {$error}");
                    $errors[] = "File {$file->getClientName()} is invalid: {$error}";
                    continue;
                }
                
                if ($file->hasMoved()) {
                    log_message('info', "File at form index {$formIndex} has already been moved");
                    continue;
                }

                $removeFlag = $removeFlags[$formIndex] ?? '0';
                if ($removeFlag === '1') {
                    log_message('info', "Skipping form index {$formIndex} - marked for removal");
                    continue;
                }

                $mime = $file->getMimeType();
                if (!in_array($mime, $allowedTypes, true)) {
                    $errors[] = "File {$file->getClientName()} has an invalid file type: {$mime}";
                    log_message('error', "Invalid file type for {$file->getClientName()}: {$mime}");
                    continue;
                }

                $safeClient = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientName());
                $newFileName = uniqid('', true) . '_' . $safeClient;
                $filePathRel = $imageBasePath . $newFileName;
                $filePathAbs = $targetDir . $newFileName;

                try {
                    $file->move($targetDir, $newFileName);
                    log_message('info', "File moved successfully to: {$filePathAbs}");
                } catch (\Throwable $e) {
                    $errors[] = "Failed to move file {$file->getClientName()}: " . $e->getMessage();
                    log_message('error', "Failed to move file: " . $e->getMessage());
                    continue;
                }

                $size = @getimagesize($filePathAbs);
                $width = $size[0] ?? null;
                $height = $size[1] ?? null;
                $checksum = @sha1_file($filePathAbs) ?: null;

                $titlePost = $titles[$formIndex] ?? '';
                $fallbackTitle = pathinfo($file->getClientName(), PATHINFO_FILENAME);
                $title = trim($titlePost) !== '' ? $titlePost : $fallbackTitle;

                $description = $descriptions[$formIndex] ?? null;
                $altText = $altTexts[$formIndex] ?? null;
                
                // Use provided order or append to the end
                $order = isset($imageOrders[$formIndex]) && $imageOrders[$formIndex] !== '' 
                    ? (int)$imageOrders[$formIndex] 
                    : ++$maxOrder;

                $isPrimary = (!$hasPrimaryImage) ? 1 : 0;
                
                $data = [
                    'item_id' => $itemId,
                    'file_path' => $filePathRel,
                    'title' => $title,
                    'description' => $description,
                    'alt_text' => $altText,
                    'image_order' => $order,
                    'is_primary' => $isPrimary,
                    'width_px' => $width,
                    'height_px' => $height,
                    'checksum_sha1' => $checksum,
                    'uploaded_at' => date('Y-m-d H:i:s'),
                ];
                
                log_message('info', "Preparing to insert image at form index {$formIndex}");
                log_message('info', "Image data: " . json_encode($data));
                
                $insertResult = $imageModel->insert($data);
                
                if (!$insertResult) {
                    $modelErrors = $imageModel->errors();
                    $errorMsg = "Failed to save image data for {$file->getClientName()}: " . json_encode($modelErrors);
                    $errors[] = $errorMsg;
                    log_message('error', $errorMsg);
                    log_message('error', "Insert returned: " . var_export($insertResult, true));
                    @unlink($filePathAbs);
                    continue;
                }
                
                $insertedId = $imageModel->getInsertID();
                log_message('info', "✓ Successfully inserted new image with ID: {$insertedId}, order: {$order}, isPrimary: {$isPrimary}");
                
                // After successfully inserting the first primary image, update the flag
                if ($isPrimary) {
                    $hasPrimaryImage = true;
                    log_message('info', "First primary image set, subsequent images will be non-primary");
                }
            }
        }

        $db->transComplete();

        $errCount = count($errors);
        log_message('info', "Image upload completed for item_id: {$itemId} with {$errCount} errors.");
        log_message('info', "Transaction status: " . ($db->transStatus() ? 'SUCCESS' : 'FAILED'));

        if ($db->transStatus() === false) {
            log_message('error', 'Transaction failed for item_id: ' . $itemId);
            log_message('error', 'Database error: ' . $db->error());
            return redirect()->back()->withInput()->with('error', 'Upload failed; transaction rolled back.')->with('errors', $errors);
        }
        
        $successMsg = $errCount > 0 
            ? 'Images processed with some errors. Check details below.' 
            : 'Images uploaded successfully.';
        
        return redirect()
            ->to(site_url('admin/dashboard'))
            ->with('success', $successMsg)
            ->with('errors', $errors);
    }

}