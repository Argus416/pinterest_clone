<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{
    public function index(PinRepository $repo): Response
    {
        $pins = $repo->findBy([] , ['updateAt' => 'DESC']);
        // $pins = $repo->findAll();
        // dd($pins);
        return $this->render('pins/index.html.twig', [
            'controller_name' => 'PinsController',
            'pins' => $pins
        ]);
    }

    public function create(Request $request): Response
    {
        $pin = new Pin;
        $form = $this->createFormBuilder($pin)
            ->add('Title')
            ->add('Description')
            ->add('Submit', SubmitType::class)
            ->getForm()
        ;

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // $data = $form->getData();
            // $pin->setTitle($data['Title']);
            // $pin->setDescription($data['Description']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($pin);
            $em->flush();
            
            return $this->redirectToRoute('index');
        }

        return $this->render('pins/create.html.twig', [
            'controller_name' => 'PinsController',
            'newPinForm' =>$form->createView()
        ]);
    }

    public function show($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Pin::class);
        $pin = $repo->find($id);
        return $this->render('pins/show.html.twig',compact('pin'));
    }

    public function edit($id, Request $request)
    {   
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository(Pin::class);
        $pin = $repo->find($id);
        
        $pin->setTitle($pin->getTitle());
        $pin->setDescription($pin->getDescription());
        
        $formEdit = $this->createFormBuilder($pin)
            ->add('Title')
            ->add('Description')
            ->add('Submit', SubmitType::class)
            ->getForm()
        ;

        $formEdit->handleRequest($request);
        if($formEdit->isSubmitted() && $formEdit->isValid()){
            $em->persist($pin);
            $em->flush();
            return $this->redirectToRoute('index');
        }
       
        return $this->render('pins/edit.html.twig',[
            'formEdit' => $formEdit->createView(),
            'pin' => $pin
        ]);
    }

}