<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\UserType;
use App\EventListener\UploadListener;

class UserController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * 
     * @Route("/edit-user/{id}", name="user.edit")
     */
    public function editUser(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()
        ->getRepository(User::class)
        ->findById($id);

        $form = $this->createForm(UserType::class, $user[0]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $user[0]->setPassword($this->passwordEncoder->encodePassword($user[0],$user[0]->getPassword()));
            $em->persist($user[0]);
            $em->flush();
        }

        return $this->render('user/edit.html.twig',[
            'user' => $user[0],
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * 
     * @Route("/create-user", name="user.create")
     */
    public function createUser(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = new User;

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword($this->passwordEncoder->encodePassword($user,$user->getPassword()));
            $em->persist($user);
            $em->flush();
        }

        return $this->render('user/edit.html.twig',[
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * 
     * @Route("/dashboard", name="user.dashboard")
     */
    public function dashboardUser()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $this->getDoctrine()
        ->getRepository(User::class)
        ->findAll();

        return $this->render('user/dashboard.html.twig',[
            'users' => $users
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * 
     * @Route("/delete/{id}", name="user.delete")
     */
    public function deleteUser($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getReference(USer::class, $id);
        $em->remove($user);
        $em->flush();

        $users = $this->getDoctrine()
        ->getRepository(User::class)
        ->findAll();

        return $this->render('user/dashboard.html.twig',[
            'users' => $users
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * 
     * @Route("/profile", name="profile")
     */
    public function profile()
    {
        return $this->render('user/profile.html.twig');
    }

}
