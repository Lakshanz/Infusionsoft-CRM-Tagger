<?php


namespace App\Repositories;


interface TagsRepositoryInterface
{
    /**
     * Get a tag by it's key
     *
     * @param string $tag Tag
     *
     * @return mixed
     */
    public function get(string $tag);
}