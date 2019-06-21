<?php


namespace Tests\Feature;


use App\Http\Helpers\InfusionsoftHelper;
use App\Repositories\ModuleRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\User;
use Tests\TestCase;

class ModuleReminderApiTest extends TestCase
{

    public function tearDown()
    {
        \Mockery::close();
    }

    public function testForRequestWithoutEmailParameter()
    {
        $response = $this->json('POST', '/api/module_reminder_assigner');
        $response
            ->assertStatus(422)
            ->assertExactJson(
                [
                    'success' => false,
                    'message' => 'Valid email address is required'
                ]
            );
    }

    public function testForRequestWithInvalidEmailFormat()
    {
        $response = $this->json(
            'POST',
            '/api/module_reminder_assigner',
            ['email' => 'test']
        );

        $response
            ->assertStatus(422)
            ->assertExactJson(
                [
                    'success' => false,
                    'message' => 'Valid email address is required'
                ]
            );
    }

    public function testForRequestForNonExistingEmail()
    {
        $mock = \Mockery::mock(UserRepositoryInterface::class);
        $mock->shouldReceive('getByEmail')
            ->with('test@test.com')
            ->once()
            ->andReturnNull();

        $this->app->instance(UserRepositoryInterface::class, $mock);

        $response = $this->json(
            'POST',
            '/api/module_reminder_assigner',
            ['email' => 'test@test.com']
        );

        $response
            ->assertStatus(404)
            ->assertExactJson(
                [
                    'success' => false,
                    'message' => 'Email does not exists'
                ]
            );
    }

    public function testForRequestWithNonExistingEmailInAPi()
    {
        $mock = \Mockery::mock(InfusionsoftHelper::class);
        $mock->shouldReceive('getContact')
            ->with('5d0b38a81c7ae@test.com')
            ->once()
            ->andReturnFalse();

        $this->app->instance(InfusionsoftHelper::class, $mock);

        $response = $this->json(
            'POST',
            '/api/module_reminder_assigner',
            ['email' => '5d0b38a81c7ae@test.com']
        );

        $response
            ->assertStatus(404)
            ->assertExactJson(
                [
                    'success' => false,
                    'message' => 'Customer not found.'
                ]
            );
    }

    public function testForCustomerWithNoProducts()
    {
        $mock = \Mockery::mock(InfusionsoftHelper::class);
        $mock->shouldReceive('getContact')
            ->with('5d0b38a81c7ae@test.com')
            ->once()
            ->andReturn(['_Products' => '']);

        $this->app->instance(InfusionsoftHelper::class, $mock);

        $response = $this->json(
            'POST',
            '/api/module_reminder_assigner',
            ['email' => '5d0b38a81c7ae@test.com']
        );

        $response
            ->assertStatus(201)
            ->assertExactJson(
                [
                    'success' => false,
                    'message' => 'No subscriptions found.'
                ]
            );
    }

    public function xtestForAllCouresesCompletedTagGeneration()
    {
        $mock = \Mockery::mock(InfusionsoftHelper::class);
        $mock->shouldReceive(
            [
                'getContact' => ['Id' => 1, '_Products' => 'ipa,iea'],
                'addTag' => true
            ]
        );
        $this->app->instance(InfusionsoftHelper::class, $mock);

        $mock = \Mockery::mock(ModuleRepositoryInterface::class);
        $mock->shouldReceive('getNextModuleId')
            ->twice()
            ->andReturnNull();
        $this->app->instance(ModuleRepositoryInterface::class, $mock);

        $response = $this->json(
            'POST',
            '/api/module_reminder_assigner',
            ['email' => '5d0b38a81c7ae@test.com']
        );

        $response
            ->assertStatus(200)
            ->assertExactJson(
                [
                    'success' => true,
                    'message' => 'Module reminders completed'
                ]
            );
    }

    public function testForNextModuleReminderTagCalculation()
    {
        $mock = \Mockery::mock(InfusionsoftHelper::class);
        $mock->shouldReceive(
            [
                'getContact' => ['Id' => 1, '_Products' => 'ipa,iea'],
                'addTag' => true
            ]
        );
        $this->app->instance(InfusionsoftHelper::class, $mock);

        $mock = \Mockery::mock(UserRepositoryInterface::class)->makePartial();

        $mock->shouldReceive('getByEmail')
            ->once()
            ->andReturn(User::find(1));

        $mock->shouldReceive('getLastCompletedModuleId')
            ->once()
            ->andReturn(4);

        $this->app->instance(UserRepositoryInterface::class, $mock);


        $response = $this->json(
            'POST',
            '/api/module_reminder_assigner',
            ['email' => '5d0b38a81c7ae@test.com']
        );

        $response
            ->assertStatus(200)
            ->assertExactJson(
                [
                    'success' => true,
                    'message' => 'Start IPA Module 3 Reminders'
                ]
            );
    }
}