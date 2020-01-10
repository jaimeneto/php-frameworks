<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApplicationController extends AbstractController
{
  /**
   * @Route("/", name="homepage")
   */
  public function index()
  {
    $response = $this->forward(
      'App\Controller\PostController::index');
    return $response;
  }
}
