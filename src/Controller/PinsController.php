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
        $pins = $repo->findAll();
        
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

}