<?php

namespace Blog;

use Zend\Router\Http\Segment;
use Blog\Model\CommentAssertions;

return [
    'view_manager' => [
        'template_path_stack' => [
            'blog' => __DIR__ . '/../view',
        ],
    ],
    'router' => [
        'routes' => [
            'post' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/post[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\PostController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'comment' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/comment[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\CommentController::class,
                        'action'     => 'index',
                    ],
                ],
             ],             
        ],
    ],
    'rbac' => [
        'permissions' => [
            'guest' => [
                'blog.post.list',
                'blog.post.view',
                'blog.comment.list',
                'blog.comment.view'
            ],
            'user' => [
                'blog.comment.add',
                'blog.comment.delete',
            ],
            'admin' => [
                'blog.post.manage',
                'blog.post.add',
                'blog.post.edit',
                'blog.post.delete',
                'blog.comment.manage',
                'blog.comment.approve'
            ]
        ],
        'assertions' => [
            Model\PostAssertions::class,
            Model\CommentAssertions::class,
        ]
    ]
];