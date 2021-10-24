<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="reg", methods={"POST"})
     */
    public function createUser(Request $request, UserRepository $users): Response
    {
        $data = json_decode($request->getContent(), true);
        if ($data == null)
        {
            return $this->json(['error_message' => "Json file is not correct"], $status = 400);
        }
        if (!isset($data['username']))
        {
            return $this->json(['error_message' => "Json file do not contains username"], $status = 400);
        }
        if (!isset($data['password']))
        {
            return $this->json(['error_message' => "Json file do not contains password"], $status = 400);
        }

        if ($data['username'] == null)
        {
            return $this->json(['error_message' => "Username empty"], $status = 400);
        }
        if ($data['password'] == null)
        {
            return $this->json(['error_message' => "Password empty"], $status = 400);
        }

        if($users->findOneByUsername($data['username']) !== null)
        {
            return $this->json(['error_message' => "User with this login already registered"], $status = 400);
        }

        $user = new User();
        $user->setUsername($data['username']);
        $user->setPassword($data['password']);

        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->flush();

        return $this->json(['data' => "You have successfully registered"], $status = 200);

    }

    /**
     * @Route("/", name="log", methods={"GET"})
     */
    public function login(Request $request, UserRepository $users): Response
    {
        $data = json_decode($request->getContent(), true);

        if ($data == null)
        {
            return $this->json(['error_message' => "Json file is not correct"], $status = 400);
        }
        if (!isset($data['username']))
        {
            return $this->json(['error_message' => "Json file do not contains username"], $status = 400);
        }
        if (!isset($data['password']))
        {
            return $this->json(['error_message' => "Json file do not contains password"], $status = 400);
        }

        if ($data['username'] == null)
        {
            return $this->json(['error_message' => "Username empty"], $status = 400);
        }
        if ($data['password'] == null)
        {
            return $this->json(['error_message' => "Password empty"], $status = 400);
        }

        $currentUser = $users->findOneByUsername($data['username']);

        if($currentUser == null || $currentUser->getPassword() !== $data['password']) {
            return $this->json(['error_message' => "Login or password incorrect"], $status = 400);
        }

        return $this->json(['data' => "You log in such as ".$data['username']], $status = 200);


    }

}
