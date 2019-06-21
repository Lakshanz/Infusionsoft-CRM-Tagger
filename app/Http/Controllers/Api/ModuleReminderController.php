<?php

namespace App\Http\Controllers\Api;


use App\Exceptions\CustomerNotFoundException;
use App\Exceptions\InfusionsoftApiErrorException;
use App\Http\Controllers\Controller;
use App\Http\Helpers\InfusionsoftHelper;
use App\Repositories\ModuleRepositoryInterface;
use App\Repositories\TagsRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\User;
use Illuminate\Http\Request;

/**
 * Class ModuleReminderController
 *
 * @package App\Http\Controllers\Api
 * @author  Lakshan Jayathilaka <hi@lakshanis.me>
 */
class ModuleReminderController extends Controller
{
    /**
     * Infusionsoft Helper
     *
     * @var InfusionsoftHelper
     */
    private $_infusionsoftHelper;

    /**
     * User repository
     *
     * @var UserRepositoryInterface
     */
    private $_userRepository;

    /**
     * Module Repository
     *
     * @var ModuleRepositoryInterface
     */
    private $_moduleRepository;


    /**
     * Tag Repository
     *
     * @var TagsRepositoryInterface
     */
    private $_tagsRepository;

    public function __construct(
        InfusionsoftHelper $infusionsoftHelper,
        UserRepositoryInterface $userRepository,
        ModuleRepositoryInterface $moduleRepository,
        TagsRepositoryInterface $tagsRepository
    ) {
        $this->_infusionsoftHelper = $infusionsoftHelper;
        $this->_userRepository = $userRepository;
        $this->_moduleRepository = $moduleRepository;
        $this->_tagsRepository = $tagsRepository;
    }

    /**
     * Assigns module reminder tags to Infusionsoft by email id provided
     *
     * @param Request $request HTTP Request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignTag(Request $request)
    {
        $email = $request->input('email');

        // Validate for email
        $validator = \Validator::make(
            $request->all(),
            ['email' => 'required|email']
        );

        if ($validator->fails()) {
            return $this->_respond(false, 'Valid email address is required', 422);
        }

        // Get customer object from database
        $customer = $this->_userRepository->getByEmail($email);

        if (!$customer) {
            return $this->_respond(false, 'Email does not exists', 404);
        }

        // Fetch customer  data from API
        try {
            $customer_data = $this->_getCustomerData($email);
        } catch (\Exception $e) {
            return $this->_respond(false, $e->getMessage(), $e->getCode());
        }

        // If user has not subscribed to any courses, we got nothing to do, return
        if ($customer_data['_Products'] == '') {
            return $this->_respond(false, "No subscriptions found.", 201);
        }

        //Calculate the next module reminder tag
        $courses_subscribed = explode(',', $customer_data['_Products']);
        $tag = $this->_getNextModuleReminderTag($customer, $courses_subscribed);

        try {
            $this->_addTag($customer_data['Id'], $tag);

        } catch (\Exception $e) {
            return $this->_respond(false, $e->getMessage(), 500);
        }

        return $this->_respond(true, $tag);
    }

    /**
     * Calculate the reminder tag
     *
     * @param User  $customer           Customer object
     * @param array $courses_subscribed Array of courses subscribed
     *
     * @return string
     */
    private function _getNextModuleReminderTag(User $customer, array $courses_subscribed)
    {
        $next_module_id = null;

        // For each subscribed course,
        // lets find out last module completed and
        // find the next module due
        foreach ($courses_subscribed as $course_key) {

            $last_completed_module_id = $this->_userRepository
                ->getLastCompletedModuleId(
                    $customer->id,
                    $course_key
                );

            $next_module_id = $this->_moduleRepository->getNextModuleId(
                $course_key,
                $last_completed_module_id
            );

            // If last completed module is the last module of the course,
            // lets move to next subscribed course
            if ($next_module_id == null) {
                continue;
            }

            break;
        }

        if ($next_module_id === null) { // user has completed all courses
            return "Module reminders completed";
        }

        $module = $this->_moduleRepository->get($next_module_id);
        return sprintf("Start %s Reminders", $module->name);
    }

    /**
     * Get the customer data from API
     *
     * @param string $email Customer Email address
     *
     * @return array
     * @throws CustomerNotFoundException
     * @throws InfusionsoftApiErrorException
     */
    private function _getCustomerData(string $email)
    {
        try {
            $customer_data = $this->_infusionsoftHelper->getContact($email);
        } catch (\Exception $e) {
            throw new InfusionsoftApiErrorException();
        }

        if ($customer_data == false) {
            throw new CustomerNotFoundException();
        }

        return $customer_data;
    }

    /**
     * Add a tag to API
     *
     * @param int    $user_id User ID
     * @param string $tag     Event Tag
     *
     * @return void
     * @throws InfusionsoftApiErrorException
     */
    private function _addTag(int $user_id, string $tag)
    {
        $tag_data = $this->_tagsRepository->get($tag);

        try {
            $this->_infusionsoftHelper->addTag($user_id, $tag_data->tag_id);
        } catch (\Exception $e) {
            throw new InfusionsoftApiErrorException();
        }
    }

    /**
     * Sends API response
     *
     * @param bool   $success Is a success response?
     * @param string $message Response status message
     * @param int    $code    HTTP status code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function _respond(bool $success, string $message, int $code = 200)
    {
        return response()->json(
            [ 'success' => $success, 'message' => $message],
            $code
        );
    }
}