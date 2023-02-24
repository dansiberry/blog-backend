<?php

use craft\elements\Entry;

return [
    'endpoints' => [
        'api/articles.json' => function() {
            return [
                'elementType' => Entry::class,
                'criteria' => ['section' => 'articles'],
                'transformer' => function(Entry $entry) {
                    return [
                        'title' => $entry->title,
                        'url' => $entry->url,
                        'textContent' => $entry->textContent
                    ];
                },
            ];
        }
    ]
];