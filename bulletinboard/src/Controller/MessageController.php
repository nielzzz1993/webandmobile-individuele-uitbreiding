<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Model\PDOMessageModel;
use App\Model\Connection;

class MessageController extends AbstractController
{
    public function __construct(Connection $connection)
    {
        $this->messageModel = new PDOMessageModel($connection);
        $this->response     = new JsonResponse();
    }

    /**
     * @Route("/messages", name="messages")
     */
    public function getAllMessages()
    {
        $this->response->setData($this->messageModel->getAllMessages());
        return $this->response;
    }

    /**
     * @route("/messages-keyword/{keyword}")
     */
    public function getMessagesByKeywords($keyword)
    {
        $this->response->setData($this->messageModel->getMessagesByKeywords($keyword));
        return $this->response;
    }

    /**
     * @route("/message-add")
     */
    public function addMessage(Request $request)
    {
        $this->response->setData($this->messageModel->addMessage(
            $request->query->get('userId'),
            $request->query->get('title'),
            $request->query->get('content'),
            $request->query->get('category')
        ));
        return $this->render('message/index.html.twig', [
            'response' => 'ok',
        ]);
    }

    /**
     * @route("/messages-category-keyword")
     */
    public function getMessagesByCategoryAndKeywords(Request $request)
    {
        $this->response->setData($this->messageModel->getMessagesByCategoryAndKeywords(
            $request->query->get('category'),
            $request->query->get('keywords')
        ));
        return $this->response;
    }

    /**
     * @route("/messages-content/{id}", requirements={"id"="\d+"})
     */
    public function getContent($id)
    {
        $this->response->setData($this->messageModel->getContent($id));
        return $this->response;
    }

    /**
     * @route("/messages-category/{id}", requirements={"id"="\d+"})
     */
    public function getCategory($id)
    {
        $this->response->setData($this->messageModel->getCategory($id));
        return $this->response;
    }

    /**
     * @route("/messages-user/{id}", requirements={"id"="\d+"})
     */
    public function getUserById($id)
    {
        $this->response->setData($this->messageModel->getUserById($id));
        return $this->response;
    }

    /**
     * @route("/messages-upvotes/{id}", requirements={"id"="\d+"})
     */
    public function getUpvotes($id)
    {
        $this->response->setData($this->messageModel->getUpvotes($id));
        return $this->response;
    }

    /**
     * @route("/messages-downvotes/{id}", requirements={"id"="\d+"})
     */
    public function getDownvotes($id)
    {
        $this->response->setData($this->messageModel->getDownvotes($id));
        return $this->response;
    }

    /**
     * @route("/messages-increase-upvotes/{id}", requirements={"id"="\d+"})
     */
    public function increaseUpvotes($id)
    {
        $this->response->setData($this->messageModel->increaseUpvotes($id));
        return $this->render('message/index.html.twig', [
            'response' => 'ok',
        ]);
    }

    /**
     * @route("/messages-increase-downvotes/{id}", requirements={"id"="\d+"})
     */
    public function increaseDownvotes($id)
    {
        $this->response->setData($this->messageModel->increaseDownvotes($id));
        return $this->render('message/index.html.twig', [
            'response' => 'ok',
        ]);
    }

    /**
     * @route("/message/{id}", requirements={"id"="\d+"})
     */
    public function getMessageById($id)
    {
        $this->response->setData($this->messageModel->getMessageById($id));
        return $this->response;
    }
}
