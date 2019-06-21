<?php


namespace App\Repositories;


use App\Tag;

/**
 * Class TagsRepository
 *
 * @package App\Repositories
 * @author  Lakshan J <hi@lakshanis.me>
 */
class TagsRepository implements TagsRepositoryInterface
{
    /**
     * Get a tag by it's key
     *
     * @param string $tag Tag
     *
     * @return Tag
     */
    public function get(string $tag)
    {
        return Tag::find($tag);
    }
}