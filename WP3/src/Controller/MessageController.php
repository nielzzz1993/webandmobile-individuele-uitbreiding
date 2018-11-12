<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\MessageRepository;
use App\Entity\Message;
use App\Repository\ReactionRepository;
use App\Entity\Reaction;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\MessageType;

class MessageController extends AbstractController
{
    /**
     * @Route("/", name="messages")
     */
    public function index()
    {
        $messages = $this->getDoctrine()
        ->getRepository(Message::class)
        ->findAll();

        if (!$messages) {
            throw $this->createNotFoundException(
                'No messages found'
            );
        }

        return $this->render(
            'message/index.html.twig',
            array( "messages"=>$messages));    
    }

    /**
     * @Route("/message/{id}", name="message")
     */
    public function message($id)
    {
        $message = $this->getDoctrine()
        ->getRepository(Message::class)
        ->findById($id);

        $reactions = $this->getDoctrine()
        ->getRepository(Reaction::class)
        ->findByMessageId($id);

        if (!$message) {
            throw $this->createNotFoundException(
                'No message found'
            );
        }

        return $this->render(
            'message/message.html.twig',
            array( "id"=>$id, "title"=>$message[0]->getTitle(), "content"=>$message[0]->getContent(),
             "category"=>$message[0]->getCategory(), "reactions"=>$reactions));    
    }

    /**
     * @Route("/messages-keyword", name="keyword", methods={"POST", "GET"})
     */
    public function getMessagesByKeywords(Request $request)
    {
        $messages = $this->getDoctrine()
        ->getRepository(Message::class)
        ->findByKeywords($request->query->get('keyword'));

        if (!$messages) {
            throw $this->createNotFoundException(
                'No message found'
            );
        }

        return $this->render(
            'message/index.html.twig',
            array( "messages"=>$messages));    
    }

    /**
     * @Route("/messages-category-keyword", name="keywordCategory")
     */
    public function getMessagesByCategoryKeywords(Request $request)
    {
        $messages = $this->getDoctrine()
        ->getRepository(Message::class)
        ->findByCategoryKeywords($request->query->get('category'),
        $request->query->get('keywords'));

        if (!$messages) {
            throw $this->createNotFoundException(
                'No message found'
            );
        }

        return $this->render(
            'message/index.html.twig',
            array( "messages"=>$messages));    
    }

    /**
     * @Route("/upvote/{id}", name="upvote", methods={"POST", "GET"})
     */
    public function postUpvote($id)
    {
        $message = $this->getDoctrine()
        ->getRepository(Message::class)
        ->findById($id);

        $upvote = $this->getDoctrine()
        ->getRepository(Message::class)
        ->doUpvote($id, $message[0]->getUpvotes());

        $messages = $this->getDoctrine()
        ->getRepository(Message::class)
        ->findAll();

        return $this->render('message/index.html.twig', [
            'messages' => $messages,
        ]);   
    }

    /**
     * @Route("/downvote/{id}", name="downvote", methods={"POST", "GET"})
     */
    public function postDownvote($id)
    {
        $message = $this->getDoctrine()
        ->getRepository(Message::class)
        ->findById($id);

        $downvote = $this->getDoctrine()
        ->getRepository(Message::class)
        ->doDownvote($id, $message[0]->getDownvotes());

        $messages = $this->getDoctrine()
        ->getRepository(Message::class)
        ->findAll();

        return $this->render('message/index.html.twig', [
            'messages' => $messages,
        ]);   
    }

    /**
     * @IsGranted("ROLE_POSTER")
     * 
     * @Route("/message-add", name="message-add", methods={"POST", "GET"})
     */
    public function postMessage(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $message = new Message;

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $message->setUser($this->getUser());
            $message->setUpvotes(0);
            $message->setDownvotes(0);

            $em->persist($message);
            $em->flush();
        }

        return $this->render('message/create.html.twig',[
            'message' => $message,
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @IsGranted("ROLE_POSTER")
     * 
     * @Route("/delete-message/{id}", name="message.delete", methods={"POST", "GET"})
     */
    public function deleteMessage($id)
    {
        $em = $this->getDoctrine()->getManager();

        $message = $em->getReference(Message::class, $id);
        $em->remove($message);
        $em->flush();

        $messages = $this->getDoctrine()
        ->getRepository(Message::class)
        ->findAll();

        return $this->render(
            'message/index.html.twig',
            array( "messages"=>$messages)); 
    }

    /**
     * @IsGranted("ROLE_POSTER")
     * 
     * @Route("/edit-message/{id}", name="message.edit", methods={"POST", "GET"})
     */
    public function editMessage(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $this->getDoctrine()
        ->getRepository(Message::class)
        ->findById($id);

        $form = $this->createForm(MessageType::class, $message[0]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($message[0]);
            $em->flush();
            
            $messages = $this->getDoctrine()
            ->getRepository(Message::class)
            ->findAll();
            
            return $this->render(
                'message/index.html.twig',
                array( "messages"=>$messages)); 
        }

        return $this->render('message/create.html.twig',[
            'message' => $message[0],
            'form' => $form->createView()
        ]); 
    }
}
