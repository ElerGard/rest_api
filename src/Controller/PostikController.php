<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Postik;
use App\Repository\PostikRepository;
use App\Repository\UserRepository;
use App\Controller\UserController;

/**
 * @Route("/postik")
 */
class PostikController extends AbstractController
{
    /**
     * @Route("/", name="allPosts", methods={"GET"})
     */
    public function findAllPosts(Request $request, UserRepository $users, PostikRepository $postikRepository): Response
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
            return new Response('Username empty', Response::HTTP_BAD_REQUEST);
        }
        if ($data['password'] == null)
        {
            return new Response('Password empty', Response::HTTP_BAD_REQUEST);
        }

        $currentUser = $users->findOneByUsername($data['username']);

        if($currentUser == null || $currentUser->getPassword() !== $data['password']) {
            return new Response('Login or password incorrect', Response::HTTP_BAD_REQUEST);;
        }
        $postiki = $postikRepository->findBy([
            "user" => $currentUser
        ]);

        foreach ($postiki as $postik) {
            $array = [
                "id" => $postik->getTitle(),
                "description" => $postik->getDescription()
            ];

            $result[] = $array;
        }

        print_r($result);

        return new Response('Posts was printed',Response::HTTP_OK);;
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     */
    public function create(Request $request, UserRepository $users): Response
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
        if (!isset($data['title']))
        {
            return new Response('Json file do not contains title', Response::HTTP_BAD_REQUEST);
        }
        if (!isset($data['description']))
        {
            return new Response('Json file do not contains description', Response::HTTP_BAD_REQUEST);
        }

        if ($data['username'] == null)
        {
            return new Response('Username empty', Response::HTTP_BAD_REQUEST);
        }
        if ($data['password'] == null)
        {
            return new Response('Password empty', Response::HTTP_BAD_REQUEST);
        }
        if ($data['title'] == null)
        {
            return new Response('Title empty', Response::HTTP_BAD_REQUEST);
        }
        if ($data['description'] == null)
        {
            return new Response('Description empty', Response::HTTP_BAD_REQUEST);
        }

        $currentUser = $users->findOneByUsername($data['username']);

        if($currentUser == null || $currentUser->getPassword() !== $data['password']) {
            return new Response('Login or password incorrect', Response::HTTP_BAD_REQUEST);;
        }

        $post = new Postik();
        $post->setTitle($data['title']);
        $post->setDescription($data['description']);
        $post->setUser($currentUser);

        $currentUser->addPostiki($post);

        $em = $this->getDoctrine()->getManager();

        $em->persist($currentUser);
        $em->persist($post);
        $em->flush();
        return new Response('Your post has been posted', Response::HTTP_OK);;

    }

    /**
     * @Route("/{id}", name="change", methods={"PUT"})
     */
    public function changePost(Request $request, UserRepository $users, PostikRepository $postikRepository, $id): Response
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
        if (!isset($data['title']))
        {
            return new Response('Json file do not contains title', Response::HTTP_BAD_REQUEST);
        }
        if (!isset($data['description']))
        {
            return new Response('Json file do not contains description', Response::HTTP_BAD_REQUEST);
        }

        if ($data['username'] == null)
        {
            return new Response('Username empty', Response::HTTP_BAD_REQUEST);
        }
        if ($data['password'] == null)
        {
            return new Response('Password empty', Response::HTTP_BAD_REQUEST);
        }
        if ($data['title'] == null)
        {
            return new Response('Title empty', Response::HTTP_BAD_REQUEST);
        }
        if ($data['description'] == null)
        {
            return new Response('Description empty', Response::HTTP_BAD_REQUEST);
        }

        $currentUser = $users->findOneByUsername($data['username']);

        if($currentUser == null || $currentUser->getPassword() !== $data['password']) {
            return new Response('Login or password incorrect', Response::HTTP_BAD_REQUEST);;
        }

        $postik = $postikRepository->find($id);


        $postik->setTitle($data['title']);
        $postik->setDescription($data['description']);

        $em = $this->getDoctrine()->getManager();

        $em->merge($postik);
        $em->flush();

        return new Response('Post was changed', Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function deletePost(Request $request, UserRepository $users, PostikRepository $postikRepository, $id): Response
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
            return new Response('Username empty', Response::HTTP_BAD_REQUEST);
        }
        if ($data['password'] == null)
        {
            return new Response('Password empty', Response::HTTP_BAD_REQUEST);
        }

        $currentUser = $users->findOneByUsername($data['username']);

        if($currentUser == null || $currentUser->getPassword() !== $data['password']) {
            return new Response('Login or password incorrect', Response::HTTP_BAD_REQUEST);;
        }

        $postik = $postikRepository->find($id);
        $currentUser->removePostiki($postik);

        $em = $this->getDoctrine()->getManager();
        $em->merge($currentUser);
        $em->remove($postik);
        $em->flush();

        return new Response('Post was deleted', Response::HTTP_OK);
    }
}
