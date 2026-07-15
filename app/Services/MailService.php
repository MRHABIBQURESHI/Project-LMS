<?php

namespace App\Services;

class MailService
{
    /**
     * Send simulated email via Mailtrap API.
     *
     * @param string $to
     * @param string $subject
     * @param string $bodyText
     * @return bool
     */
    public function sendMailtrapEmail($to, $subject, $bodyText)
    {
        // Try sending via cPanel SMTP first
        try {
            \Illuminate\Support\Facades\Mail::raw($bodyText, function ($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            $log_dir = public_path('uploads');
            if (!file_exists($log_dir)) {
                mkdir($log_dir, 0777, true);
            }
            file_put_contents($log_dir . '/emails.txt', "cPanel SMTP Connection Failure: " . $e->getMessage() . " (Falling back to Mailtrap API)\n", FILE_APPEND);
        }

        $sandbox_id = getenv('MAILTRAP_INBOX_ID') ?: '4763995';
        $api_token = getenv('MAILTRAP_TOKEN') ?: 'cd9fda3ed30ebcbd5ca752147ae0d539';

        $url = "https://sandbox.api.mailtrap.io/api/send/{$sandbox_id}";

        $data = [
            "from" => ["email" => "registry@cpduk.london", "name" => "CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD"],
            "to" => [["email" => $to]],
            "subject" => $subject,
            "text" => $bodyText
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Bypass SSL verification peer on Windows local environments
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$api_token}",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Log details to email log file for debug
        $log_dir = public_path('uploads');
        if (!file_exists($log_dir)) {
            mkdir($log_dir, 0777, true);
        }
        if ($response === false) {
            $err = curl_error($ch);
            file_put_contents($log_dir . '/emails.txt', "Mailtrap cURL Failure: " . $err . "\n", FILE_APPEND);
        } else {
            file_put_contents($log_dir . '/emails.txt', "Mailtrap Response Code: " . $httpCode . ", Body: " . $response . "\n", FILE_APPEND);
        }
        
        curl_close($ch);

        return ($httpCode === 200);
    }

    /**
     * Send simulated WhatsApp message via Twilio API.
     *
     * @param string $to
     * @param string $message
     * @return bool
     */
    public function sendWhatsApp($to, $message)
    {
        $log_dir = public_path('uploads');
        if (!file_exists($log_dir)) {
            mkdir($log_dir, 0777, true);
        }
        $log_entry = "========================================\n" .
                     "Twilio WhatsApp API Simulation\n" .
                     "To: " . $to . "\n" .
                     "Date: " . date('Y-m-d H:i:s') . "\n" .
                     "Message: " . $message . "\n" .
                     "========================================\n\n";
        file_put_contents($log_dir . '/whatsapp_alerts.txt', $log_entry, FILE_APPEND);
        return true;
    }
}
