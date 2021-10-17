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
            return new Response('Json file is not correct', Response::HTTP_BAD_REQUEST);
        }
        if (!isset($data['username']))
        {
            return new Response('Json file do not contains username', Response::HTTP_BAD_REQUEST);
        }
        if (!isset($data['password']))
        {
            return new Response('Json file do not contains password', Response::HTTP_BAD_REQUEST);
        }

        if ($data['username'] == null)
        {
            return new Response('Username empty', Response::HTTP_BAD_REQUEST);;
        }
        if ($data['password'] == null)
        {
            return new Response('Password empty', Response::HTTP_BAD_REQUEST);;
        }

        if($users->findOneByUsername($data['username']) !== null) {
            return new Response('User with this login already registered', Response::HTTP_BAD_REQUEST);;
        }

        $user = new User();
        $user->setUsername($data['username']);
        $user->setPassword($data['password']);

        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->flush();

        return new Response('You have successfully registered', Response::HTTP_OK);;

    }

    /**
     * @Route("/", name="log", methods={"GET"})
     */
    public function login(Request $request, UserRepository $users): Response
    {
        $data = json_decode($request->getContent(), true);

        if ($data == null)
        {
            return new Response('Json file is not correct', Response::HTTP_BAD_REQUEST);
        }
        if (!isset($data['username']))
        {
            return new Response('Json file do not contains username', Response::HTTP_BAD_REQUEST);
        }
        if (!isset($data['password']))
        {
            return new Response('Json file do not contains password', Response::HTTP_BAD_REQUEST);
        }

        if ($data['username'] == null)
        {
            return new Response('Username empty', Response::HTTP_BAD_REQUEST);;
        }
        if ($data['password'] == null)
        {
            return new Response('Password empty', Response::HTTP_BAD_REQUEST);;
        }

        $currentUser = $users->findOneByUsername($data['username']);

        if($currentUser == null || $currentUser->getPassword() !== $data['password']) {
            return new Response('Login or password incorrect', Response::HTTP_BAD_REQUEST);;
        }

        return new Response('You log in such as '.$data['username'], Response::HTTP_OK);;

    }

}
