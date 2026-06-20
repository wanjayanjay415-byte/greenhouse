<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        $loginUrl = base_url('auth/manager');

        if (!$session->get('isLoggedIn')) {
            return redirect()->to($loginUrl)->with('error', 'Silakan masuk terlebih dahulu.');
        }

        // Cek Role jika diberikan argument, contoh: filter => auth:manager
        if ($arguments) {
            $userRole = $session->get('role');
            if (!in_array($userRole, $arguments)) {
                return redirect()->to($loginUrl)->with('error', 'Akses ditolak: Anda tidak memiliki hak akses role tersebut.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
