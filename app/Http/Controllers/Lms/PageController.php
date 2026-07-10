<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PageController extends Controller
{
    /**
     * Display the terms page.
     */
    public function terms()
    {
        return view('lms.terms');
    }

    /**
     * Display the privacy policy page.
     */
    public function privacy()
    {
        return view('lms.privacy');
    }

    /**
     * Display the contact and onboarding page.
     */
    public function contact()
    {
        return view('lms.contact', [
            'success' => '',
            'error' => '',
            'generated_code' => ''
        ]);    }

    /**
     * Handle affiliate application onboarding form post.
     */
    public function applyAffiliate(Request $request)
    {
        $name = trim($request->input('rep_name', ''));
        $email = trim($request->input('rep_email', ''));
        $whatsapp = trim($request->input('rep_whatsapp', ''));
        $region = trim($request->input('rep_region', ''));
        $experience = trim($request->input('rep_experience', ''));
        $volume = trim($request->input('rep_volume', ''));

        $error = '';
        $success = '';
        $generatedCode = '';

        if (empty($name) || empty($email) || empty($whatsapp) || empty($region) || empty($volume)) {
            $error = 'Please fill in all the required fields in the onboarding form.';
        } else {
            try {
                // Generate unique representative code
                $repCode = 'REP-LDN-' . strtoupper(substr(md5(uniqid()), 0, 5));

                $contactInfo = "Email: $email, WhatsApp: $whatsapp, Region: $region, Experience: $experience, Expected Volume: $volume";

                DB::insert("
                    INSERT INTO affiliates (name, rep_code, contact_info, application_status, linked_students_count)
                    VALUES (?, ?, ?, 'pending', 0)
                ", [$name, $repCode, $contactInfo]);

                $success = 'Your application for onboarding has been registered successfully. Awaiting administrator review.';
                $generatedCode = $repCode;
            } catch (Exception $e) {
                $error = 'Database error during onboarding submission: ' . $e->getMessage();
            }
        }

        return view('lms.contact', [
            'success' => $success,
            'error' => $error,
            'generated_code' => $generatedCode
        ]);
    }
}
