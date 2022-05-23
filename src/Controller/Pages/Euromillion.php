<?php

namespace App\Controller\Pages;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Euromillion extends AbstractController
{
    /**
     * @Route(path="/test", name="random_numbers")
     * @return void
     * @throws Exception
     */
    public function __invoke(): Response
    {
        $generate = static function($min, $max, $endLoop) {
            $list = [];
            $i = 1;
            while($i <= $endLoop) {
                $number = random_int($min, $max);
                if (!in_array($number, $list, true)) {
                    $list[] = $number;
                    $i++;
                }
            }

            return $list;
        };

        $first = $generate(1, 50, 5);
        $second = $generate(1, 11, 2);

        $content = implode(' - ', $first) . ' / ' . implode(' - ', $second);

        return new Response($content);
    }

}
