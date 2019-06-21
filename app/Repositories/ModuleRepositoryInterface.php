<?php


namespace App\Repositories;

/**
 * Interface ModuleRepositoryInterface
 *
 * @package App\Repositories
 * @author  Lakshan J <hi@lakshanis.me>
 */
interface ModuleRepositoryInterface
{
    /**
     * Get a module by it's ID
     *
     * @param int $module_id Module's ID
     *
     * @return mixed
     */
    public function get(int $module_id);

    /**
     * Get the ID of next module due after a completed module
     *
     * @param string $course_key        Course Key
     * @param int    $current_module_id Current Module's ID
     *
     * @return int|null
     */
    public function getNextModuleId($course_key, $current_module_id);
}