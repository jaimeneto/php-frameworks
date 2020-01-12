<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class StringTruncate extends AbstractHelper
{
   public function __invoke($string, $width, $ellipsis = '...')
   {
       return strtok(
           wordwrap($string, $width, $ellipsis . PHP_EOL),
           PHP_EOL
       );
    }
}