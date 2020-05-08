<?php

namespace App\Controller;

use App\Entity\Example;
use App\Entity\SecondExample;
use App\Repository\ExampleRepository;
use App\Repository\SecondExampleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SlowExampleController extends AbstractController
{
    /**
     * @Route("/slow/example", name="api_get_slow_example", methods={"GET"})
     */
    public function getSlowExample(SecondExampleRepository $secondExampleRepository): Response
    {
        sleep((mt_rand(0, 10) / 10));
        $secondExampleRepository->findAll();

        return new Response('', Response::HTTP_OK);
    }

    /**
     * @Route("/slow/example", name="api_post_slow_example", methods={"POST"})
     */
    public function postSlowExample(EntityManagerInterface $entityManager): Response
    {
        for ($i = 0; $i < mt_rand(5, 10); $i++) {
            $example = new SecondExample();
            $example->setEg('some data');
            $entityManager->persist($example);
            $entityManager->flush();
        }

        for ($i = 0; $i < mt_rand(2, 5); $i++) {
            $example = new Example();
            $example->setFoo('some data');
            $entityManager->persist($example);
            $entityManager->flush();
        }

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/slow/example", name="api_put_slow_example", methods={"PUT"})
     */
    public function putSlowExample(
        EntityManagerInterface $entityManager,
        SecondExampleRepository $secondExampleRepository
    ): Response {
        $data = $secondExampleRepository->findAll();
        foreach ($data as $element) {
            $element->setEg('new data');
        }
        $entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/slow/example", name="api_delete_slow_example", methods={"DELETE"})
     */
    public function deleteSlowExample(
        EntityManagerInterface $entityManager,
        ExampleRepository $exampleRepository
    ): Response {
        $data = $exampleRepository->findAll();
        foreach ($data as $element) {
            $entityManager->remove($element);
        }
        $entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
