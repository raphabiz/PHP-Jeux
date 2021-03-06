<?php

namespace App\Controller;

use App\Form\CreateQuestionType;
use App\Service\Encoder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Quizz;




class AdminController extends AbstractController
{
    /**
     * @Route("/admin/createQ",name="add_question")
     */
    public function addQuizzQuestion(Request $request,EntityManagerInterface $manager)
    {
        $quizz = new Quizz();
        $form=$this->createForm(CreateQuestionType::class,$quizz);
        $form->handleRequest($request);
        
        if($form->isSubmitted()&&$form->isValid()){
    
            $this->manager=$manager;
            $this->manager->persist($quizz);
            $this->manager->flush();

        }
        return $this->render('admin/quizzQuestions.html.twig' ,[
             'form' => $form->createView()
             ]);
    }
}