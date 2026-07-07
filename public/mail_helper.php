<?php
require_once __DIR__ . '/db.php';

if (!function_exists('sendMailtrapEmail')) {
    function sendMailtrapEmail($to, $subject, $body_text) {
        $sandbox_id = getenv('MAILTRAP_INBOX_ID') ?: '4763995';
        $api_token = getenv('MAILTRAP_TOKEN') ?: 'cd9fda3ed30ebcbd5ca752147ae0d539';

        $url = "https://sandbox.api.mailtrap.io/api/send/{$sandbox_id}";

        $data = [
            "from" => ["email" => "registry@liab-edu.org", "name" => "UK London International Award Board"],
            "to" => [["email" => $to]],
            "subject" => $subject,
            "text" => $body_text
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$api_token}",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode === 200);
    }
}
