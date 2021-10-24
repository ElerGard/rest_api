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
    public function getAllPosts(Request $request, UserRepository $users, PostikRepository $postikRepository): Response
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
        $postiki = $postikRepository->findBy([
            "user" => $currentUser
        ]);

        if ($postiki == null)
        {
            return $this->json(['data' => "No posts"], $status = 200);
        }

        foreach ($postiki as $postik) {
            $array = [
                "id" => $postik->getId(),
                "title" => $postik->getTitle(),
                "description" => $postik->getDescription()
            ];

            $result[] = $array;
        }

        return $this->json(['data' => $result], $status = 200);
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     */
    public function create(Request $request, UserRepository $users): Response
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
        if (!isset($data['title']))
        {
            return $this->json(['error_message' => "Json file do not contains title"], $status = 400);
        }
        if (!isset($data['description']))
        {
            return $this->json(['error_message' => "Json file do not contains description"], $status = 400);
        }

        if ($data['username'] == null)
        {
            return $this->json(['error_message' => "Username empty"], $status = 400);
        }
        if ($data['password'] == null)
        {
            return $this->json(['error_message' => "Password empty"], $status = 400);
        }
        if ($data['title'] == null)
        {
            return $this->json(['error_message' => "Title empty"], $status = 400);
        }
        if ($data['description'] == null)
        {
            return $this->json(['error_message' => "Description empty"], $status = 400);
        }


        $currentUser = $users->findOneByUsername($data['username']);

        if($currentUser == null || $currentUser->getPassword() !== $data['password']) {
            return $this->json(['error_message' => "Login or password incorrect"], $status = 400);
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
        return $this->json(['data' => "Your post has been posted"], $status = 200);
    }

    /**
     * @Route("/{id}", name="change", methods={"PUT"})
     */
    public function changePost(Request $request, UserRepository $users, PostikRepository $postikRepository, $id): Response
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
        if (!isset($data['title']))
        {
            return $this->json(['error_message' => "Json file do not contains title"], $status = 400);
        }
        if (!isset($data['description']))
        {
            return $this->json(['error_message' => "Json file do not contains description"], $status = 400);
        }

        if ($data['username'] == null)
        {
            return $this->json(['error_message' => "Username empty"], $status = 400);
        }
        if ($data['password'] == null)
        {
            return $this->json(['error_message' => "Password empty"], $status = 400);
        }
        if ($data['title'] == null)
        {
            return $this->json(['error_message' => "Title empty"], $status = 400);
        }
        if ($data['description'] == null)
        {
            return $this->json(['error_message' => "Description empty"], $status = 400);
        }

        $currentUser = $users->findOneByUsername($data['username']);

        if($currentUser == null || $currentUser->getPassword() !== $data['password']) {
            return $this->json(['error_message' => "Login or password incorrect"], $status = 400);
        }

        $postik = $postikRepository->find($id);

        if ($postik == null)
        {
            return $this->json(['error_message' => "This post is not exist"], $status = 400);
        }

        if ($currentUser !== $postik->getUser())
        {
            return $this->json(['error_message' => "This post is not yours"], $status = 400);
        }

        $postik->setTitle($data['title']);
        $postik->setDescription($data['description']);

        $em = $this->getDoctrine()->getManager();

        $em->merge($postik);
        $em->flush();

        return $this->json(['data' => "Post was changed"], $status = 200);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function deletePost(Request $request, UserRepository $users, PostikRepository $postikRepository, $id): Response
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

        $postik = $postikRepository->find($id);

        if ($postik == null)
        {
            return $this->json(['error_message' => "This post is not exist"], $status = 400);
        }

        if ($currentUser !== $postik->getUser())
        {
            return $this->json(['error_message' => "This post is not yours"], $status = 400);
        }

        $currentUser->removePostiki($postik);

        $em = $this->getDoctrine()->getManager();
        $em->merge($currentUser);
        $em->remove($postik);
        $em->flush();

        return $this->json(['data' => "Post was deleted"], $status = 200);
    }
}
