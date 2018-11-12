<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\MessageRepository;
use App\Entity\Message;
use App\Repository\ReactionRepository;
use App\Entity\Reaction;

class ReactionController extends AbstractController
{
    /**
     * @Route("/reaction", name="reaction")
     */
    public function index()
    {
        return $this->render('reaction/index.html.twig', [
            'controller_name' => 'ReactionController',
        ]);
    }

    /**
     * @Route("/reaction-add/{id}", name="add-reaction", methods={"POST", "GET"})
     */
    public function addReaction($id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $message = $this->getDoctrine()
        ->getRepository(Message::class)
        ->findById($id);

        $reaction = new Reaction();
        $reaction->setContent($request->query->get('content'));
        $reaction->setToken("token");
        $reaction->setMessage($message[0]);

        $entityManager->persist($reaction);

        $entityManager->flush();

        return $this->render('reaction/index.html.twig', [
            'reaction' => $reaction,
            'message' => $message[0]
        ]); 
    }

    /**
     * @Route("/reaction-change/{id}", name="add-reaction", methods={"PUT", "GET"})
     */
    public function changeReaction($id, Request $request)
    {
        $reaction = $this->getDoctrine()
        ->getRepository(Reaction::class)
        ->findById($id);
        $newReaction = new Reaction();

        if($reaction[0]->getToken()==$request->query->get('token'))
        {
            $entityManager = $this->getDoctrine()->getManager();

            $newReaction->setContent($request->query->get('content'));
            $newReaction->setToken($reaction[0]->getToken());
            $newReaction->setMessage($reaction[0]->getMessage());
            $newReaction->setId($id);

            $entityManager->merge($newReaction);

            $entityManager->flush();

            return $this->render('reaction/reactionChanged.html.twig', [
                'newReaction' => $newReaction,
                'error' => false
            ]); 
        }
        else
        {
            return $this->render('reaction/reactionChanged.html.twig', [
                'newReaction' => $newReaction,
                'error' => true
            ]); 
        }
    }
}
