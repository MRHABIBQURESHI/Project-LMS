<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\DB;
use Exception;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (session()->has('user_id')) {
            return redirect()->route('lms.dashboard');
        }
        return view('lms.login');
    }

    /**
     * Handle login post action.
     */
    public function login(Request $request)
    {
        try {
            $user = $this->authService->login($request->input('email'), $request->input('password'));
            
            // Set sessions
            session([
                'user_id' => $user['id'],
                'user_name' => $user['full_name'],
                'user_email' => $user['email'],
                'user_role' => $user['role'],
            ]);

            return redirect()->route('lms.dashboard');
        } catch (Exception $e) {
            return view('lms.login', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Log out of the portal session.
     */
    public function logout()
    {
        session()->flush();
        return redirect()->route('lms.login');
    }

    /**
     * Show candidate registration portal.
     */
    public function showRegister()
    {
        if (session()->has('user_id')) {
            return redirect()->route('lms.dashboard');
        }
        
        try {
            $facs = DB::select("SELECT * FROM faculties");
        } catch (Exception $e) {
            $facs = [];
        }

        return view('lms.register', ['facs' => $facs]);
    }

    /**
     * Process registration request.
     */
    public function register(Request $request)
    {
        try {
            $data = $request->all();
            $file = $request->file('id_document') ?? $request->file('id_document');
            
            // If file upload using native array fallback
            if (!$file && isset($_FILES['id_document'])) {
                $file = $_FILES['id_document'];
            }

            $result = $this->authService->registerStudent($data, $file);

            if ($result['payment_choice'] === 'cash') {
                session([
                    'temp_user_id' => $result['user_id'],
                    'temp_email' => $result['email'],
                    'temp_password' => $result['password'],
                    'temp_full_name' => $result['full_name'],
                ]);
                return redirect()->route('lms.remittance');
            } else {
                return view('lms.register', [
                    'success' => true,
                    'email' => $result['email'],
                    'password' => $result['password'],
                    'full_name' => $result['full_name'],
                    'facs' => DB::select("SELECT * FROM faculties")
                ]);
            }
        } catch (Exception $e) {
            return view('lms.register', [
                'error' => $e->getMessage(),
                'facs' => DB::select("SELECT * FROM faculties")
            ]);
        }
    }

    /**
     * Show password recovery request page.
     */
    public function showForgotPassword()
    {
        return view('lms.forgot-password');
    }

    /**
     * Generate password reset request token details.
     */
    public function forgotPassword(Request $request)
    {
        $email = $request->input('email');
        try {
            $token = $this->authService->forgotPassword($email);
            
            // Save token details in session for validation
            session([
                'reset_email' => $email,
                'reset_token' => $token
            ]);

            $resetLink = route('lms.reset-password') . '?email=' . urlencode($email) . '&token=' . $token;

            return view('lms.forgot-password', [
                'success' => 'We have sent a simulated password reset link to your email.',
                'reset_link' => $resetLink
            ]);
        } catch (Exception $e) {
            return view('lms.forgot-password', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Show reset password validation form.
     */
    public function showResetPassword(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');

        $valid = false;
        if (session('reset_email') === $email && session('reset_token') === $token) {
            $valid = true;
        }

        return view('lms.reset-password', [
            'email' => $email,
            'token' => $token,
            'valid_request' => $valid,
            'error' => $valid ? '' : 'Invalid or expired password reset link. Please request a new link.'
        ]);
    }

    /**
     * Execute password update action.
     */
    public function resetPassword(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');
        
        $valid = (session('reset_email') === $email && session('reset_token') === $token);

        if (!$valid) {
            return view('lms.reset-password', [
                'email' => $email,
                'token' => $token,
                'valid_request' => false,
                'error' => 'Invalid or expired password reset link. Please request a new link.'
            ]);
        }

        $password = $request->input('password');
        $confirmPassword = $request->input('confirm_password');

        if (empty($password) || empty($confirmPassword)) {
            $error = 'Please fill in both password fields.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match. Re-type the password.';
        } else {
            try {
                $this->authService->resetPassword($email, $password);
                
                // Clear sessions
                session()->forget(['reset_email', 'reset_token']);
                
                return view('lms.reset-password', ['success' => true]);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        return view('lms.reset-password', [
            'email' => $email,
            'token' => $token,
            'valid_request' => true,
            'error' => $error
        ]);
    }
}
