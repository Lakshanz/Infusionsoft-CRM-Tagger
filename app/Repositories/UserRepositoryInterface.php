<?php


namespace App\Repositories;

/**
 * Interface UserRepositoryInterface
 *
 * @package App\Repositories
 * @author  Lakshan J <hi@lakshanis.me>
 */
interface UserRepositoryInterface
{
    /**
     * Get a user by it's ID
     *
     * @param int $user_id User ID
     *
     * @return mixed
     */
    public function get(int $user_id);

    /**
     * Get a user by it's email
     *
     * @param string $email User email address
     *
     * @return mixed
     */
    public function getByEmail(string $email);

    /**
     * Get user's last completed module by course
     *
     * @param int    $user_id    User's ID
     * @param string $course_key Course Key
     *
     * @return int
     */
    public function getLastCompletedModuleId(int $user_id, string $course_key);
}