<?php


namespace App\Repositories;


use App\User;

/**
 * Class UserRepository
 *
 * @package App\Repositories
 * @author  Lakshan J <hi@lakshanis.me>
 */
class UserRepository implements UserRepositoryInterface
{

    /**
     * Get a user by it's ID
     *
     * @param int $user_id User's ID
     *
     * @return User|null
     */
    public function get(int $user_id)
    {
        return User::find($user_id);
    }

    /**
     * Get a User by Email Address
     *
     * @param string $email Email Address
     *
     * @return User|null
     */
    public function getByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    /**
     * Get user's last completed module by course
     *
     * @param int    $user_id    User's ID
     * @param string $course_key Course Key
     *
     * @return int
     */
    public function getLastCompletedModuleId(int $user_id, string $course_key)
    {
        $id = \DB::table('user_completed_modules')
            ->join('modules', 'user_completed_modules.module_id', '=', 'modules.id')
            ->where('modules.course_key', $course_key)
            ->where('user_completed_modules.user_id', $user_id)
            ->max('module_id');

        if (!$id) { // User has not completed any
            return 0;
        }

        return $id;
    }
}