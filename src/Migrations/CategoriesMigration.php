<?php

namespace Callisto\Migrations;

use Plenty\Modules\Category\Contracts\CategoryRepositoryContract;
use Plenty\Modules\Category\Contracts\CategoryTemplateRepositoryContract;

/**
 * Class CreateCategoriesMigration
 * @package LayoutCore\Migrations
 */
class CategoriesMigration
{
    /**
     * @var CategoryRepositoryContract
     */
    private $categoryRepo;
    
    /**
     * @var CategoryTemplateRepositoryContract
     */
    private $categoryTemplateRepo;
    
    /**
     * @var ConfigRepository
     */
    private $configRepo;
    
    /**
     * @var array
     */
    private $categoriesToCreate = [
        'global.category.home' => [
            'de' => 'Startseite',
            'en' => 'Startpage'
        ],
        'global.category.privacy_policy' => [
            'de' => 'Datenschutz',
            'en' => 'Privacy policy'
        ],
        'global.category.terms_and_conditions' => [
            'de' => 'AGB',
            'en' => 'Terms and conditions'
        ],
        'global.category.legal_disclosure' => [
            'de' => 'Impressum',
            'en' => 'Legal disclosure'
        ],
        'global.category.page_not_found' => [
            'de' => '404',
            'en' => '404'
        ],
    ];
    
    /**
     * CategoriesMigration constructor.
     * @param CategoryRepositoryContract $categoryRepo
     * @param CategoryTemplateRepositoryContract $categoryTemplateRepo
     */
    public function __construct(CategoryRepositoryContract $categoryRepo,
                                CategoryTemplateRepositoryContract $categoryTemplateRepo)
    {
        $this->categoryRepo = $categoryRepo;
        $this->categoryTemplateRepo = $categoryTemplateRepo;
    }
    
    /**
     * Create all standard categories needed for callisto 4
     */
    public function run()
    {
        $parentCategoryId = $this->createParentCategory();
        
        foreach($this->categoriesToCreate as $configKey => $categories)
        {
            $details = [];
            foreach($categories as $lang => $name)
            {
                $details[] = [
                    'plentyId' => 0,
                    'lang' => $lang,
                    'name' => $name
                ];
            }
            
            $base = [
                'parentCategoryId' => $parentCategoryId,
                'level' => 0,
                'type' => 'content',
                'linklist' => 'Y',
                'right' => 'all',
                'sitemap' => 'Y',
                'details' => $details,
                'clients' => [
                    [
                        'plentyId' => 0
                    ]
                ]
            ];
            
            $newCategory = $this->categoryRepo->createCategory($base);
    
            foreach($categories as $lang => $name)
            {
                $categoryTemplate = [
                    'content' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
                    'categoryId' => $newCategory->id,
                    'lang' => $lang,
                    'webstoreId' => 0
                ];
    
                $this->categoryTemplateRepo->storeCategoryTemplateContent($categoryTemplate['content'], $categoryTemplate['categoryId'], $categoryTemplate['lang'], $categoryTemplate['webstoreId']);
            }
        }
    }
    
    /**
     * Create the callisto 4 parent category
     * @return int
     */
    private function createParentCategory()
    {
        $details = [
            [
                'plentyId' => 0,
                'lang' => 'de',
                'name' => 'callisto_4'
            ],
            [
                'plentyId' => 0,
                'lang' => 'en',
                'name' => 'callisto_4'
            ]
        ];
        
        $base = [
            'level' => 0,
            'type' => 'content',
            'linklist' => 'Y',
            'right' => 'all',
            'sitemap' => 'Y',
            'details' => $details,
            'clients' => [
                [
                    'plentyId' => 0
                ]
	        ]
        ];
    
        $parentCategory = $this->categoryRepo->createCategory($base);
        
        return $parentCategory->id;
    }
}
