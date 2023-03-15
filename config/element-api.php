<?php

use craft\elements\Entry;
use craft\elements\Category;

return [
    'endpoints' => [
        'api/articles.json' => function() {
            Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            Craft::$app->response->headers->set('Access-Control-Allow-Headers', 'Content-Type');

            $limit = Craft::$app->request->getQueryParam('limit');
            $category = Craft::$app->request->getQueryParam('category-id');
            $query = Craft::$app->request->getQueryParam('query');
            return [
                'elementType' => Entry::class,
                'criteria' => [
                    'section' => 'articles',
                    'relatedTo' => $category ? $category : '',
                    'search' => $query ? "title:'*#{$query}*' OR textContent:'*#{$query}*'" : null,
                    'limit' => $limit ? $limit : 100
                ],
                'paginate' => false,
                'transformer' => function(Entry $entry) {
                    foreach ($entry->articleCategory->all() as $category) {
                        $categories[] = [
                            'id' => $category->id,
                            'title' => $category->title
                        ];
                    }
                    
                    return [
                        'id' => $entry->id,
                        'title' => $entry->title,
                        'slug' => $entry->slug,
                        'imageUrl' => $entry->image->one()->url,
                        'imageSmallUrl' => $entry->image->one()->getUrl('fit500'),
                        'categories' => $categories,
                        'createdAt' => $entry->postDate->format('c')
                    ];
                },
            ];
        },
        'api/article/<slug>.json' => function($slug) {
            Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            Craft::$app->response->headers->set('Access-Control-Allow-Headers', 'Content-Type');

            return [
                'elementType' => Entry::class,
                'criteria' => [
                    'section' => 'articles',
                    'slug' => $slug
                ],
                'one' => true,
                'transformer' => function(Entry $entry) {
                    $categories = [];

                    foreach ($entry->articleCategory->all() as $category) {
                        $categories[] = [
                            'id' => $category->id,
                            'title' => $category->title
                        ];
                    }

                    return [
                        'id' => $entry->id,
                        'title' => $entry->title,
                        'slug' => $entry->slug,
                        'imageUrl' => $entry->image->one()->url,
                        'imageSmallUrl' => $entry->image->one()->getUrl('fit500'),
                        'categories' => $categories,
                        'fullText' => $entry->textContent,
                        'createdAt' => $entry->postDate->format('c'),
                        'authorName' => $entry->articleAuthor->one()->title,
                        'authorImage' => $entry->articleAuthor->one()->image->one()->getUrl('fit200')
                    ];
                },
            ];
        },
        'api/categories.json' => function() {
            Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            Craft::$app->response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
            return [
                'elementType' => Category::class,
                'criteria' => ['group' => 'blogCategories'],
                'transformer' => function(Category $category) {
                    return [
                        'id' => $category->id,
                        'title' => $category->title
                    ];
                },
            ];
        }
    ]
];