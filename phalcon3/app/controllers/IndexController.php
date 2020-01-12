<?php

use Phalcon\Paginator\Adapter\Model as Paginator;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        $posts = Posts::find(['order' => 'id DESC']);

        $numberPage = $this->request->getQuery('page', 'int');

        $paginator = new Paginator([
            'data' => $posts,
            'limit' => 10,
            'page' => $numberPage ?: 1
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    public function showPostAction($id)
    {
        $post = Posts::findFirstById($id);
        if (!$post) {
            return $this->response->redirect('');
        }

        $this->tag->prependTitle($post->getTitle() . ' | ');

        $this->view->post = $post;
    }
    
}
