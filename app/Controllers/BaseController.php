<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
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
        $this->helpers = ['cookie', 'url', 'form', 'stock'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Auto-login via Remember Me cookie if session is dead
        $session = session();
        if (!$session->get('isLoggedIn')) {
            $cookie = get_cookie('remember_token');
            if ($cookie) {
                $decoded = base64_decode($cookie);
                if (strpos($decoded, ':') !== false) {
                    list($userId, $hash) = explode(':', $decoded);
                    $userModel = new \App\Models\UserModel();
                    $user = $userModel->find($userId);
                    
                    if ($user && $user['status'] === 'active') {
                        $expectedHash = hash('sha256', $user['id'] . $user['password_hash'] . env('app.rememberSecret', 'GH_SecretKey_991'));
                        if (hash_equals($expectedHash, $hash)) {
                            // Pulihkan sesi
                            $session->set([
                                'id' => $user['id'],
                                'full_name' => $user['full_name'],
                                'email' => $user['email'],
                                'role' => $user['role'],
                                'isLoggedIn' => true
                            ]);
                            // Perbarui masa aktif cookie
                            set_cookie('remember_token', $cookie, 2592000);
                        }
                    }
                }
            }
        }
    }
}
