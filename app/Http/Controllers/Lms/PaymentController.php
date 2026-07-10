<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use Exception;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Show manual cash remittance form.
     */
    public function showRemittance()
    {
        $userId = 0;
        $linkedEmail = '';
        $generatedPassword = '';

        if (session()->has('user_id')) {
            $userId = session('user_id');
            $linkedEmail = session('user_email');
        } elseif (session()->has('temp_user_id')) {
            $userId = session('temp_user_id');
            $linkedEmail = session('temp_email');
            $generatedPassword = session('temp_password');
        }

        return view('lms.remittance', [
            'user_id' => $userId,
            'linked_email' => $linkedEmail,
            'generated_password' => $generatedPassword,
            'success' => false,
            'error' => '',
        ]);
    }

    /**
     * Handle manual remittance post.
     */
    public function submitRemittance(Request $request)
    {
        $userId = 0;
        $linkedEmail = '';
        $generatedPassword = '';

        if (session()->has('user_id')) {
            $userId = session('user_id');
            $linkedEmail = session('user_email');
        } elseif (session()->has('temp_user_id')) {
            $userId = session('temp_user_id');
            $linkedEmail = session('temp_email');
            $generatedPassword = session('temp_password');
        }

        $senderName = $request->input('sender_name');
        $transactionRef = $request->input('transaction_ref');
        $amount = floatval($request->input('amount'));
        $method = $request->input('method');
        $email = $request->input('email');

        try {
            $result = $this->paymentService->submitRemittance(
                $userId,
                $senderName,
                $transactionRef,
                $amount,
                $method,
                $email
            );

            // If temp session was active, clean it but preserve data for response screen
            if (session()->has('temp_user_id')) {
                session()->forget(['temp_user_id', 'temp_email', 'temp_password', 'temp_full_name']);
            }

            return view('lms.remittance', [
                'user_id' => $result['user_id'],
                'linked_email' => $linkedEmail ?: $email,
                'generated_password' => $generatedPassword,
                'success' => true,
                'error' => '',
            ]);
        } catch (Exception $e) {
            return view('lms.remittance', [
                'user_id' => $userId,
                'linked_email' => $linkedEmail,
                'generated_password' => $generatedPassword,
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show tuition checkout selection gateway page.
     */
    public function showCheckout()
    {
        if (!session()->has('temp_user_id')) {
            return redirect()->route('lms.register');
        }

        return view('lms.checkout', [
            'email' => session('temp_email'),
            'password' => session('temp_password'),
            'full_name' => session('temp_full_name'),
            'success' => false,
            'error' => '',
        ]);
    }

    /**
     * Process Stripe checkout payment upfront choice.
     */
    public function processCheckout(Request $request)
    {
        if (!session()->has('temp_user_id')) {
            return redirect()->route('lms.register');
        }

        $userId = session('temp_user_id');
        $email = session('temp_email');
        $password = session('temp_password');
        $fullName = session('temp_full_name');

        try {
            $this->paymentService->processCheckoutStripe($userId);
            
            // Clear temp registration sessions
            session()->forget(['temp_user_id', 'temp_email', 'temp_password', 'temp_full_name']);

            return view('lms.checkout', [
                'email' => $email,
                'password' => $password,
                'full_name' => $fullName,
                'success' => true,
                'error' => '',
            ]);
        } catch (Exception $e) {
            return view('lms.checkout', [
                'email' => $email,
                'password' => $password,
                'full_name' => $fullName,
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle incoming webhook requests.
     */
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('HTTP_STRIPE_SIGNATURE', '');

        try {
            $this->paymentService->handleStripeWebhook($payload, $sigHeader);
            return response('Webhook Processed', 200);
        } catch (Exception $e) {
            return response($e->getMessage(), 400);
        }
    }
}
