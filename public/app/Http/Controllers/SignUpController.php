<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\PaymentDto;
use App\DTOs\UserDto;
use App\Exceptions\PaymentClientException;
use App\Processors\WunderMobilityPaymentProcessor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SignUpController extends Controller
{
    public function __construct(private WunderMobilityPaymentProcessor $paymentProcessor)
    {
    }

    public function index()
    {
        return view('signup');
    }

    public function signUp(Request $request): Response
    {
        $paymentDto = new PaymentDto(
            iban: $request['iban'],
            owner: $request['owner'],
        );

        $userDto = new UserDto(
            firstName: $request['firstName'],
            lastName: $request['lastName'],
            telephone: $request['telephone'],
            street: $request['street'],
            house_number: $request['house_number'],
            zip_code: (int)$request['zip_code'],
            city: $request['city'],
            paymentDto: $paymentDto
        );

        try {
            $response = $this->paymentProcessor->registerPayment($userDto);
        } catch (PaymentClientException $paymentClientException) {
            return new Response('An error occurred while calling an 3 party api.', Response::HTTP_FAILED_DEPENDENCY);
        }

        return new Response($response, Response::HTTP_CREATED);
    }
}
