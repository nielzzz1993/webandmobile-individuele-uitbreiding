<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Model\PDOReactionModel;
use App\Model\Connection;

class ReactionController extends AbstractController
{

    public function __construct(Connection $connection)
    {
        $this->reactionModel = new PDOReactionModel($connection);
        $this->response      = new JsonResponse();
    }

    /**
     * @route("/reaction-add")
     */
    public function addReaction(Request $request)
    {
        $this->response->setData($this->reactionModel->addReaction(
            $request->query->get('messageId'),
            $request->query->get('reaction')
        ));
        return $this->render('reaction/index.html.twig', [
            'response' => $this->response,
        ]);
    }

    /**
     * @route("/reaction-content/{id}", requirements={"id"="\d+"})
     */
    public function getContent($id)
    {
        $this->response->setData($this->reactionModel->getContent($id));
        return $this->response;
        /*return $this->render('reaction/index.html.twig', [
            'response' => $this->response,
        ]);*/
    }

    /**
     * @route("/reaction-token/{id}", requirements={"id"="\d+"})
     */
    public function getToken($id)
    {
        $this->response->setData($this->reactionModel->getToken($id));
        return $this->render('reaction/index.html.twig', [
            'response' => $this->response,
        ]);
    }

    /**
     * @route("/reaction-message/{id}", requirements={"id"="\d+"})
     */
    public function getMessageById($id)
    {
        $this->response->setData($this->reactionModel->getMessageById($id));
        return $this->render('reaction/index.html.twig', [
            'response' => $this->response,
        ]);
    }
}
