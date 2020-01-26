<?php

namespace App\Controller;

use App\Entity\SecondExample;
use App\Repository\SecondExampleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecondExampleController extends AbstractController
{
    /**
     * @Route("/second/example", name="api_get_second_example", methods={"GET"})
     */
    public function getSecondExample(SecondExampleRepository $secondExampleRepository): Response
    {
        $secondExampleRepository->findAll();

        return new Response('', Response::HTTP_OK);
    }

    /**
     * @Route("/second/example", name="api_post_second_example", methods={"POST"})
     */
    public function postSecondExample(EntityManagerInterface $entityManager): Response
    {
        $example = new SecondExample();
        $example->setEg('some data');
        $entityManager->persist($example);
        $entityManager->flush();

        $example = new SecondExample();
        $example->setEg('some data');
        $entityManager->persist($example);
        $entityManager->flush();

        $example = new SecondExample();
        $example->setEg('some data');
        $entityManager->persist($example);
        $entityManager->flush();

        return new Response('', Response::HTTP_CREATED);
    }
}
