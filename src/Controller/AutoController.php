<?php

namespace App\Controller;

use App\Entity\Auto;
use App\Form\AutoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AutoController extends AbstractController{

    #[Route('/', name: 'home')]
    public function show(EntityManagerInterface $doctrine): Response
    {
        $auto = $doctrine->getRepository(Auto::class)->findAll();


        return $this->render('auto/index.html.twig', ['autos' => $auto]);
    }
    #[Route('/insert', name: 'InsertCar')]
    public function showInsert(Request $request, EntityManagerInterface $entityManager): Response
    {
        $auto = $entityManager->getRepository(Auto::class)->findAll();
        $add = new Auto();
        $form = $this->createForm(AutoType::class, $add);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $add = $form->getData();
            $entityManager->persist($add);
            $entityManager->flush();
            return $this->redirectToRoute('home');
            
        }

        return $this->renderForm('insert.html.twig', [
            'controller_name' => 'AutoController',
            'form' => $form
        ]);



    }

        #[Route('detail/{id}', name: 'detailCar')]
    public function showDetails(EntityManagerInterface $doctrine, int $id): Response
    {
        $car = $doctrine->getRepository(Auto::class)->find($id);

        return $this->render('detail.html.twig', [
            'cars' => $car
        ]);

    }

    #[Route('update/{id}', name: 'updateCar')]
    public function updateAction(Auto $auto, Request $request, EntityManagerInterface $entityManager)
    {
        if (!$auto) {
            throw $this->createNotFoundException('Auto not found');
        }

        $form = $this->createForm(AutoType::class, $auto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash(
                'succes',
                'The car has been updated succesfully'
            );

            return $this->redirectToRoute('home');
        }
        return $this->renderForm('insert.html.twig', ['form' => $form]);
    }

    #[Route('delete/{id}', name: 'deleteCar')]
    public function showDelete(EntityManagerInterface $entityManager, int $id,): Response
    {
        $auto = $entityManager->getRepository(Auto::class)->find($id);
        {


            $entityManager->remove($auto);
            $entityManager->flush();
            $this->addFlash(
                'notice',
                'Het item is verwijderd'
            );
            return $this->redirectToRoute('home');
        }



        return $this->render('delete.html.twig', [
            'autos' => $auto
        ]);

    }
}

