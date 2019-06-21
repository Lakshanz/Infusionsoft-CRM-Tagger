<?php


namespace App\Repositories;

use App\Module;

/**
 * Class ModuleRepository
 *
 * @package App\Repositories
 * @author  Lakshan J <hi@lakshanis.me>
 */
class ModuleRepository implements ModuleRepositoryInterface
{
    /**
     * Get a module by it's ID
     *
     * @param int $module_id Module's ID
     *
     * @return Module|null
     */
    public function get(int $module_id)
    {
        return Module::find($module_id);
    }

    /**
     * Get the ID of next module due after a completed module
     *
     * @param string $course_key        Course Key
     * @param int    $current_module_id Current Module's ID
     *
     * @return int|null
     */
    public function getNextModuleId($course_key, $current_module_id)
    {
        $next_id = Module::where('course_key', $course_key)
            ->where('id', '>', $current_module_id)
            ->min('id');

        // If current is the last module id course
        if (!$next_id) {
            return null;
        }

        return $next_id;
    }
}