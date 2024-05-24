<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category as CategoryEntity;

class Category extends Fixture
{
    const CATEGORIES = [
        'All' => [],
        'Vacations' => [
            'Travel',
            'Hotel',
            'Camping'
        ],
        'Job' => [
            'Job',
            'Internship',
            'Freelance'
        ],
        'Vehicle' => [
            'Car',
            'Motorbike',
            'Boat'
        ],
        'Property' => [
            'House',
            'Apartment',
            'Parking'
        ],
        'Fashion' => [
            'Clothing',
            'Shoes',
            'Accessories'
        ],
        'Home' => [
            'Kitchen',
            'Bathroom',
            'Furniture'
        ],
        'Multimedia' => [
            'Audio',
            'Video',
            'Photo'
        ],
        'Hobbies' => [
            'Sport',
            'Music',
            'Book'
        ],
        'Animals' => [
            'Pet',
            'Wild',
            'Domestic'
        ],
        'Professional equipment' => [
            'Tool',
            'Computer',
            'Phone'
        ],
        'Services' => [
            'Cleaning',
            'Cooking',
            'Carpentry'
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CATEGORIES as $categoryName => $subCategoryNames) {
            $category = new CategoryEntity();
            $category->setName($categoryName);
            $manager->persist($category);

            foreach ($subCategoryNames as $subCategoryName) {
                $subCategory = new CategoryEntity();
                $subCategory->setName($categoryName . '/' . $subCategoryName);
                $manager->persist($subCategory);
            }
        }
        $manager->flush();
    }
}
