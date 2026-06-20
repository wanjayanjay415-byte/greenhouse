<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function index()
    {
        // Default redirect ke customer auth jika buka root
        return redirect()->to(base_url('auth/user'));
    }

    public function user()
    {
        return view('auth/customer_auth');
    }

    public function manager()
    {
        return view('auth/manager_auth');
    }

    public function owner()
    {
        // Portal Owner dihapus — owner login lewat portal Admin/Manager.
        return redirect()->to(base_url('auth/manager'));
    }

    public function loginProcess()
    {
        $session = session();
        $model = new \App\Models\UserModel();
        
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        
        $user = $model->where('email', $email)->first();
        
        if ($user) {
            // Cek status aktif/suspended
            if ($user['status'] !== 'active') {
                return redirect()->back()->with('error', 'Akun Anda telah dinonaktifkan (Suspended). Hubungi administrator.');
            }

            // Verifikasi Password Hash (Menggunakan password_verify bawaan PHP)
            if (password_verify($password, $user['password_hash'])) {
                $sessionData = [
                    'id' => $user['id'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'isLoggedIn' => true
                ];
                $session->set($sessionData);
                
                // Set Remember Me Cookie
                if ($this->request->getPost('remember')) {
                    helper('cookie');
                    $secret = env('app.rememberSecret', 'GH_SecretKey_991');
                    $tokenString = $user['id'] . ':' . hash('sha256', $user['id'] . $user['password_hash'] . $secret);
                    set_cookie('remember_token', base64_encode($tokenString), 2592000); // 30 days
                }

                // Redirect sesuai Role (manager & owner ke dashboard data center)
                if (in_array($user['role'], ['manager', 'owner', 'admin'])) {
                    return redirect()->to(base_url('manager'))->with('success', 'Selamat datang kembali, Administrator!');
                } else {
                    return redirect()->to(base_url('/'));
                }
            } else {
                return redirect()->back()->with('error', 'Kombinasi password anda salah.');
            }
        } else {
            return redirect()->back()->with('error', 'Email tidak ditemukan dalam sistem.');
        }
    }

    public function logout()
    {
        $role = session()->get('role');
        session()->destroy();
        
        helper('cookie');
        delete_cookie('remember_token');
        if ($role == 'customer') {
            return redirect()->to(base_url('auth/user'));
        } else {
            // Manager & Owner sama-sama lewat portal Admin/Manager
            return redirect()->to(base_url('auth/manager'));
        }
    }

    public function registerProcess()
    {
        $model = new \App\Models\UserModel();
        
        // Cek apakah email sudah terdaftar
        $existing = $model->where('email', $this->request->getPost('email'))->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Email sudah terdaftar. Silakan login.');
        }

        $model->insert([
            'full_name'     => $this->request->getPost('full_name'),
            'email'         => $this->request->getPost('email'),
            'phone'         => $this->request->getPost('phone'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'          => 'customer',
            'status'        => 'active',
        ]);

        return redirect()->to(base_url('auth/user'))->with('success', 'Registrasi berhasil! Silakan login.');
    }
}
