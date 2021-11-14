<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
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
        $pins = $repo->findBy(['is_deleted' => 0] , ['updateAt' => 'DESC']);
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
        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            // $pin->setTitle($data['Title']);
            // $pin->setDescription($data['Description']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($pin);
            $em->flush();
            
            return $this->redirectToRoute('index');
        }

        return $this->render('pins/create.html.twig', [
            'controller_name' => 'PinsController',
            'form' =>$form->createView()
        ]);
    }

    public function show($id, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Pin::class);
        $pin = $repo->find($id);

        $formDelete = $this->createFormBuilder($pin)->getForm();

        $formDelete->handleRequest($request);
        
        if($formDelete->isSubmitted() && $formDelete->isValid()){
            $pin->setIsDeleted(true);
            $em->persist($pin);
            $em->flush();
            return $this->redirectToRoute('index');
        }
        ($pin);
        return $this->render('pins/show.html.twig',[
            'pin' => $pin,
            'formDelete' => $formDelete->createView()
        ]);
    }

    public function edit($id, Request $request)
    {   
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository(Pin::class);
        $pin = $repo->find($id);
        
        $pin->setTitle($pin->getTitle());
        $pin->setDescription($pin->getDescription());
        
        $formEdit = $this->createForm(PinType::class, $pin);

        $formEdit->handleRequest($request);
        if($formEdit->isSubmitted() && $formEdit->isValid()){
            $em->flush();
            return $this->redirectToRoute('index');
        }
       
        return $this->render('pins/edit.html.twig',[
            'form' => $formEdit->createView(),
            'pin' => $pin
        ]);
    }

}