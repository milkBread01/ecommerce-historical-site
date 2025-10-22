<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\Navigation;

class ViewDataFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get the view renderer
        $view = \Config\Services::renderer();
        
        // Get navigation data
        $navigationController = new Navigation();
        $navigationData = $navigationController->getNavigationData();
        
        // Get cart count from session
        $cart = session()->get('cart') ?? [];
        $cartCount = 0;
        if (!empty($cart)) {
            foreach ($cart as $item) {
                $cartCount += $item['quantity'] ?? 1;
            }
        }
        
        // Get user information
        $userName = session()->get('username') ?? null;
        $userId = session()->get('user_id') ?? null;
        $isLoggedIn = !empty($userId);
        
        // Set global view data that will be available in all views
        $view->setData([
            'navigationData' => $navigationData,
            'cartCount'      => $cartCount,
            'userName'       => $userName,
            'userId'         => $userId,
            'isLoggedIn'     => $isLoggedIn,
        ], 'raw'); // 'raw' context means data won't be escaped automatically
        
        // No response means continue normal execution
        return $request;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after
        return $response;
    }
}