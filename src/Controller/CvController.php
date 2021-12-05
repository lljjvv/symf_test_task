<?php

namespace App\Controller;

use App\Entity\CV;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CvController extends AbstractController
{
    /**
     * @Route("/cv", name="cv")
     */
    public function index(): Response
    {
        return $this->render('cv/index.html.twig', [
            'controller_name' => 'CvController',
        ]);
    }

    /**
     * @Route("/cv/create", name="create_cv")
     */
    public function createCV(ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $entityManager = $doctrine->getManager();

        $CV = new CV();
        $CV->setName('');
        $CV->setAddress(NULL);
        $CV->setEducation(NULL);
        $CV->setExperience(NULL);
        $CV->setWork('');

        $errors = $validator->validate($CV);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        $entityManager->persist($CV);
        $entityManager->flush();

        return new Response('Saved new cv with id ' . $CV->getId());
    }
}
