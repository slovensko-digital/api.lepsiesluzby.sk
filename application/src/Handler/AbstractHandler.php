<?php

declare(strict_types=1);

namespace App\Handler;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AbstractHandler implements HandlerInterface
{
    protected function validRecaptcha(?string $token, ?string $action) {
        $secretKey = getenv('recaptcha_secret');

        if (isset($_POST['email']) && $_POST['email']) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
        } else {
            // set error message and redirect back to form...
            header('location: subscribe_newsletter_form.php');
            exit;
        }

        // call curl to POST request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $secretKey, 'response' => $token)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $arrResponse = json_decode($response, true);

        // verify the response
        if($arrResponse["success"] == '1' && $arrResponse["action"] == $action && $arrResponse["score"] >= 0.5) {
            return true;
        } else {
            return false;
        }
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
}