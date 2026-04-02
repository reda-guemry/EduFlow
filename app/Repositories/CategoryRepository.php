<?php


namespace App\Repositories;

use App\Models\Category;


class CategoryRepository
{

    public function __construct(
    
    ) {}

    public function getAllCategories()
    {
        return Category::all();
    }

}

