<?php
declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Http\Client\WunderMobilityPaymentClient;
use App\Http\Controllers\SignUpController;
use App\Processors\WunderMobilityPaymentProcessor;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Log\Logger;
use Tests\TestCase;

class SignUpControllerTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function testSignUp()
    {
        /** @var SignUpController $signUpController */
        $signUpController = $this->app->make(SignUpController::class);

        $request = new Request();

        $request["firstName"] = "Thomas";
        $request["lastName"] = "Kaestner";
        $request["telephone"] = "0157702348";
        $request["street"] = "Hammer Baum";
        $request["house_number"] = "28";
        $request["zip_code"] = 20537;
        $request["city"] = "Hamburg";
        $request["customerId"] = 1;
        $request["iban"] = "DE8219A001";
        $request["owner"] = "Thomas Kaestner";

        $response = $signUpController->signUp($request);

        $this->assertInstanceOf(Response::class, $response);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $this->assertDatabaseHas('users', [
            "first_name" => "Thomas",
            "last_name" => "Kaestner",
            "telephone" => "0157702348",
            "street" => "Hammer Baum",
            "house_number" => "28",
            "zip_code" => 20537,
            "city" => "Hamburg",
            "account_owner" => "Thomas Kaestner",
            "iban" => "DE8219A001",
            "customer_id" => 1,
        ]);
    }

    public function testSignUpExceptionThrown()
    {
        $client = \Mockery::mock(Client::class)
            ->shouldReceive('post')
            ->andThrow(new \Exception())
            ->getMock();

        $logger = \Mockery::mock(Logger::class)
            ->shouldReceive('warning')
            ->once()
            ->getMock();

        $wunderMobilityPaymentClient = new WunderMobilityPaymentClient($client, $logger);

        $wunderMobilityPaymentProcessor = new WunderMobilityPaymentProcessor($wunderMobilityPaymentClient);

        $signUpController = new SignUpController($wunderMobilityPaymentProcessor);

        $request = new Request();

        $request["firstName"] = "Thomas";
        $request["lastName"] = "Kaestner";
        $request["telephone"] = "0157702348";
        $request["street"] = "Hammer Baum";
        $request["house_number"] = "28";
        $request["zip_code"] = 20537;
        $request["city"] = "Hamburg";
        $request["customerId"] = 1;
        $request["iban"] = "DE8219A001";
        $request["owner"] = "Thomas Kaestner";

        $response = $signUpController->signUp($request);

        $this->assertEquals(Response::HTTP_FAILED_DEPENDENCY, $response->getStatusCode());

        $this->assertInstanceOf(Response::class, $response);
    }
}
