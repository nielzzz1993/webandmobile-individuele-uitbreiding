<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Model\PDOUserModel;
use App\Model\Connection;

class UserController extends AbstractController
{

    public function __construct(Connection $connection)
    {
        $this->messageModel = new PDOUserModel($connection);
        $this->response     = new JsonResponse();
    }

    /**
     * @Route("/user-add")
     */
    public function addUser(Request $request)
    {
        $this->response->setData($this->messageModel->addUser(
            $request->query->get('username'),
            $request->query->get('password'),
            $request->query->get('role')
        ));

        return $this->render('message/index.html.twig', [
            'response' => 'ok',
        ]);
    }

    /**
     * @Route("/user-role/{id}", requirements={"id"="\d+"})
     */
    public function getRole($id)
    {
        $this->response->setData($this->messageModel->getRole($id));
        return $this->response;
        /*return $this->render('message/index.html.twig', [
            'response' => $this->response,
        ]);*/
    }

    /**
     * @Route("/user-messages/{id}", requirements={"id"="\d+"})
     */
    public function getMessageById($id)
    {
        $this->response->setData($this->messageModel->getMessageById($id));
        return $this->render('message/index.html.twig', [
            'response' => $this->response,
        ]);
    }
}
