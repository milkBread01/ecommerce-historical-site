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
    public function index(){
        return view('admin_views/admin_dash');
    }

    public function new_product_details(){
        $allCategories = (new CategoryModel())->where('is_visible', 1)->findAll();
        $categories = $this->organizeCategories($allCategories);

        // Check if redirecting from new collection creation
        $preSelectedCollectionId = $this->request->getGet('collection_id');
        $isNewCollection = $this->request->getGet('new_collection') === '1';

        $data = [
            'categories' => $categories,
            'itemInformation'  => (new ItemModel())->findAll(),
            'itemGeneralSpecs' => (new ItemGeneralInfoModel())->findAll(),
            'collections'      => (new CollectionModel())->findAll(),
            'preSelectedCollectionId' => $preSelectedCollectionId,
            'isNewCollection' => $isNewCollection,
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
        $entryType = $this->request->getPost('entry_type');

        // ---- HANDLE COLLECTION CREATION ----
        if ($entryType === 'create_collection') {
            $rules = [
                'collection_name' => 'required|min_length[3]|max_length[255]|is_unique[collections.collection_name]',
                'collection_description' => 'required|min_length[10]',
                'is_bundle_only' => 'required|in_list[0,1]',
            ];
            
            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('validation', $this->validator);
            }
            
            $collectionData = [
                'collection_name' => $this->request->getPost('collection_name'),
                'collection_slug' => url_title($this->request->getPost('collection_name')),
                'collection_description' => $this->request->getPost('collection_description'),
                'is_bundle_only' => $this->request->getPost('is_bundle_only'),
                'bundle_price' => $this->request->getPost('collection_price') ?: null,
            ];
            
            $collectionId = (new CollectionModel())->insert($collectionData);
            
            if (!$collectionId) {
                return redirect()->back()->withInput()->with('error', 'Failed to create collection.');
            }
            
            // Redirect back with collection pre-selected, prompt to add first item
            return redirect()->to('admin/new-product-details?collection_id=' . $collectionId . '&new_collection=1')
                    ->with('success', 'Collection created successfully. Now add items to it.');
        }

        // ---- HANDLE INDIVIDUAL ITEM (NEW OR ASSIGNED TO COLLECTION) ----
        
        // Load models
        $itemGeneral   = new ItemGeneralInfoModel();
        $itemModel     = new ItemModel();
        $categoryModel = new CategoryModel();

        $post = $this->request->getPost();

        // Normalize booleans
        $post['on_sale']         = isset($post['on_sale']) ? 1 : 0;
        $post['is_featured']     = isset($post['is_featured']) ? 1 : 0;
        $post['visible']         = isset($post['visible']) ? 1 : 0;
        $post['documentation']   = isset($post['documentation']) ? 1 : 0;
        $post['certificate_auth']= isset($post['certificate_auth']) ? 1 : 0;

        // Validate item data
        $rules = $itemModel->getValidationRules() ?? [];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Category validation
        $categoryId = (int)($post['category_id'] ?? 0);
        if (!$categoryId || !$categoryModel->find($categoryId)) {
            return redirect()->back()->withInput()->with('error', 'Category is required and must exist.');
        }

        // Process discount
        $discountedPrice = null;
        $price = (float)($post['price'] ?? 0);
        
        if (!empty($post['amount_discount'])) {
            $discountedPrice = (float)$post['amount_discount'];
        } elseif (!empty($post['percentage_discount']) && $post['on_sale']) {
            $percentageOff = (float)$post['percentage_discount'];
            $discountedPrice = $price - ($price * ($percentageOff / 100));
        }

        // Process materials
        $materials = array_filter([
            trim($post['material_1'] ?? ''),
            trim($post['material_2'] ?? ''),
            trim($post['material_3'] ?? '')
        ], function($val) { return $val !== '' && $val !== null; });
        $materialsStr = !empty($materials) ? implode(', ', $materials) : null;

        // Process markings
        $markings = array_filter([
            trim($post['marking_1'] ?? ''),
            trim($post['marking_2'] ?? ''),
            trim($post['marking_3'] ?? '')
        ], function($val) { return $val !== '' && $val !== null; });
        $markingsStr = !empty($markings) ? implode(', ', $markings) : null;

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
        }

        // Process weight
        $weightStr = null;
        if (!empty($post['weight_value'])) {
            $weightStr = sprintf(
                "%s %s",
                $post['weight_value'],
                $post['weight_unit'] ?? 'kg'
            );
        }

        // Build item payload
        $itemData = [
            'category_id'     => $categoryId,
            'collection_id'   => $this->request->getPost('collection_id') ?: null,

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

        // Build general info payload
        $generalData = [
            'era_period'        => $post['era_period'] ?? null,
            'country_origin'    => $post['country_origin'] ?? null,
            'branch_org'        => $post['branch_org'] ?? null,
            'unit_regiment'     => $post['unit_regiment'] ?? null,
            'authenticity'      => $post['authenticity'] ?? null,
            'condition'         => $post['condition'] ?? null,
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

        // Transaction: insert both or rollback
        $db = \Config\Database::connect();
        $db->transStart();

        if (!$itemModel->insert($itemData)) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 
                'Could not save item: ' . implode('; ', $itemModel->errors() ?: [])
            );
        }
        
        $itemId = $itemModel->getInsertID();
        if (!$itemId) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Could not retrieve inserted item ID');
        }

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
            return redirect()->back()->withInput()->with('error', 'Transaction failed while saving product.');
        }

        // get parent slug and send to savePartials, if slug not found in function it will return null
        $parentID = $categoryModel
            ->select('parent_id')
            ->where('category_id', $categoryId)
            ->first()['parent_id'] ?? null;
        if($parentID == null) $parentID = $categoryId;

        $parentSlug = $categoryModel
            ->select('slug')
            ->where('category_id', $parentID)
            ->first()['slug'] ?? null;

        $partialSave = $this->savePartials($parentSlug, (int)$itemId);
        if ($partialSave instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $partialSave; // propagate redirect on error
        }

        return redirect()
            ->to(site_url('admin/new-product-images?item_id=' . $itemId))
            ->with('success', 'Product details saved. You can now upload images.');
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

        
        log_message('info', 'Received AJAX request for category_id: ' . $category_id);
        log_message('info', 'slug: ' . $slug);
        log_message('info', 'parent ID: ' . $parentID);
        log_message('info', 'parent slug: ' . $parentSlug);
        log_message('info', 'admin_views/product_creation/partials/' . $parentSlug);

        // use slug to call the view file with the same name

        $data = [
            'itemInformation'  => (new ItemModel())->findAll(),
            'itemGeneralSpecs' => (new ItemGeneralInfoModel())->findAll(),
        ];

        try{
            return view('admin_views/product_creation/partials/' . $parentSlug, $data);
        }catch(\Exception $e){
            
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

        // Check if a primary image already exists for this item
        $existingPrimary = $imageModel->where('item_id', $itemId)
                               ->where('is_primary', 1)
                               ->first();
        log_message('info', 'Existing primary image for item_id ' . $itemId . ': ' . ($existingPrimary ? 'yes' : 'no'));

        $allImages = $imageModel->where('item_id', $itemId)->findAll();
        log_message('info', 'Total images for item_id ' . $itemId . ': ' . count($allImages));
        foreach ($allImages as $img) {
            log_message('info', 'Image: ' . $img['image_id'] . ', is_primary: ' . $img['is_primary']);
        }

        $hasPrimaryImage = $existingPrimary !== null;

        // get uploaded files
        $files = $this->request->getFiles();
        if (empty($files['product_images'])) {
            log_message('error', 'No files uploaded for item_id: ' . $itemId);
            return redirect()->back()->withInput()->with('error', 'No images were uploaded.');
        }

        // get uploaded files array
        $uploadedFiles = $files['product_images'];
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

        $targetDir     = FCPATH . $imageBasePath;
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }


        log_message('info', 'Uploading images for item_id: ' . $itemId . ' under path: ' . $imageBasePath);

        $errors = [];

        /*
        'item_id',
        'file_path',      // e.g. uploads/products/123/main.jpg
        'url',            // optional CDN/external URL
        'title',
        'description',
        'alt_text',
        'image_order',    // integer sort within item
        'is_primary',     // tinyint(1)
        'width_px',
        'height_px',
        'checksum_sha1',
        'uploaded_at',    // if your schema stores this timestamp
        */

        $db = \Config\Database::connect();
        $db->transStart();

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        // for each uploaded file
        foreach ($uploadedFiles as $index => $file) {
            if (!$file->isValid() || $file->hasMoved()) {
                continue;
            }

            $remove = $this->request->getPost("remove[$index]") ?? '0';
            if ($remove === '1') { continue; }

            $mime = $file->getMimeType();
            if (!in_array($mime, $allowedTypes, true)) {
                $errors[] = "File {$file->getClientName()} has an invalid file type.";
                continue;
            }

            $safeClient = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientName());
            $newFileName = uniqid('', true) . '_' . $safeClient;
            $filePathRel = $imageBasePath . $newFileName;
            $filePathAbs = $targetDir . $newFileName;

            try {
                $file->move($targetDir, $newFileName);
            } catch (\Throwable $e) {
                $errors[] = "Failed to move file {$file->getClientName()}: " . $e->getMessage();
                continue;
            }

            $size = @getimagesize($filePathAbs);
            $width  = $size[0] ?? null;
            $height = $size[1] ?? null;
            $checksum = @sha1_file($filePathAbs) ?: null;

            $titlePost = $this->request->getPost("title[$index]") ?: '';
            $fallbackTitle = pathinfo($file->getClientName(), PATHINFO_FILENAME);
            $title = trim($titlePost) !== '' ? $titlePost : $fallbackTitle;

            $description = $this->request->getPost("description[$index]") ?: null;
            $altText     = $this->request->getPost("alt_text[$index]") ?: null;
            $order = (int) ($this->request->getPost("image_order[$index]") ?? $index);

            // ✅ Only set first image as primary if no primary exists yet
            $isPrimary = (!$hasPrimaryImage && $index === 0) ? 1 : 0;

            $data = [
                'item_id'       => $itemId,
                'file_path'     => $filePathRel,
                'title'         => $title,
                'description'   => $description,
                'alt_text'      => $altText,
                'image_order'   => $order,
                'is_primary'    => $isPrimary,  // ✅ Fixed logic
                'width_px'      => $width,
                'height_px'     => $height,
                'checksum_sha1' => $checksum,
                'uploaded_at'   => date('Y-m-d H:i:s'),
            ];

            if (!$imageModel->insert($data)) {
                $errors[] = "Failed to save image data for {$file->getClientName()}: " . implode('; ', $imageModel->errors() ?: []);
                @unlink($filePathAbs);
                continue;
            }
            
            // ✅ After successfully inserting the first primary image, update the flag
            if ($isPrimary) {
                $hasPrimaryImage = true;
            }
        }

        $db->transComplete();

        $errCount = count($errors);
        log_message('info', "Image upload completed for item_id: {$itemId} with {$errCount} errors.");

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Upload failed; transaction rolled back.')->with('errors', $errors);
        }
        
        return redirect()
            ->to(site_url('admin/dashboard'))
            ->with('success', 'Images uploaded successfully.')
            ->with('errors', $errors);
    }


}