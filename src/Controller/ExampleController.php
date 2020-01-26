<?php

namespace App\Controller;

use App\Entity\Example;
use App\Repository\ExampleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExampleController extends AbstractController
{
    /**
     * @Route("/example", name="api_get_example", methods={"GET"})
     */
    public function getExample(ExampleRepository $exampleRepository): Response
    {
        $exampleRepository->findAll();

        return new Response('', Response::HTTP_OK);
    }

    /**
     * @Route("/example", name="api_post_example", methods={"POST"})
     */
    public function postExample(EntityManagerInterface $entityManager): Response
    {
        $example = new Example();
        $example->setFoo('some data');
        $entityManager->persist($example);
        $entityManager->flush();

        return new Response('', Response::HTTP_CREATED);
    }
}
