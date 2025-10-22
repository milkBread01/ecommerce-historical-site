<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\CategoryModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    protected $viewData = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        //$this->loadNavigationData();

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
    }

    /* protected function loadNavigationData(): void
    {
        $categoryModel = new CategoryModel();
        
        $parentCategories = $categoryModel
            ->where('is_visible', 1)
            ->where('parent_id', null)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

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

        // Store in viewData array that all controllers can access
        $this->viewData['navigationData'] = $navigationData;
        $this->viewData['userName'] = session()->get('username');
        $this->viewData['cartCount'] = session()->get('cart_count') ?? 0;
    }

    // Helper method to merge controller data with base view data
    protected function view(string $name, array $data = [], array $options = []): string
    {
        // Merge controller-specific data with base view data
        $data = array_merge($this->viewData, $data);
        return view($name, $data, $options);
    } */
}
