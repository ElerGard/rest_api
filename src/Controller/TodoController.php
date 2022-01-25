<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Todo;
use App\Repository\TodoRepository;
use App\Repository\UserRepository;
use App\Controller\UserController;

/**
 * @Route("/todo")
 */
class TodoController extends AbstractController
{
    /**
     * @Route("/", name="allTodo", methods={"GET"})
     */
    public function getAllTodo(Request $request, UserRepository $users, TodoRepository $TodoRepository): Response
    {
        $data['username'] = $request->headers->get('php-auth-user');
        $data['password'] = $request->headers->get('php-auth-pw');
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
        $Todos = $TodoRepository->findBy([
            "user" => $currentUser
        ]);

        if ($Todos == null)
        {
            return $this->json(['data' => "No todo"], $status = 200);
        }

        foreach ($Todos as $Todo) {
            $array = [
                "id" => $Todo->getId(),
                "title" => $Todo->getTitle(),
                "description" => $Todo->getDescription()
            ];

            $result[] = $array;
        }

        return $this->json($result, $status = 200);
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     */
    public function createTodo(Request $request, UserRepository $users): Response
    {

        $data = json_decode($request->getContent(), true);
        $data['username'] = $request->headers->get('php-auth-user');
        $data['password'] = $request->headers->get('php-auth-pw');
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

        $Todo = new Todo();
        $Todo->setTitle($data['title']);
        $Todo->setDescription($data['description']);
        $Todo->setUser($currentUser);

        $currentUser->addTodo($Todo);

        $em = $this->getDoctrine()->getManager();

        $em->persist($currentUser);
        $em->persist($Todo);
        $em->flush();
        return $this->json(['data' => "Your todo has been created"], $status = 200);
    }

    /**
     * @Route("/{id}", name="change", methods={"PUT"})
     */
    public function changeTodo(Request $request, UserRepository $users, TodoRepository $TodoRepository, $id): Response
    {
        $data = json_decode($request->getContent(), true);
        $data['username'] = $request->headers->get('php-auth-user');
        $data['password'] = $request->headers->get('php-auth-pw');
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

        $Todo = $TodoRepository->find($id);

        if ($Todo == null)
        {
            return $this->json(['error_message' => "This todo is not exist"], $status = 400);
        }

        if ($currentUser !== $Todo->getUser())
        {
            return $this->json(['error_message' => "This todo is not yours"], $status = 400);
        }

        $Todo->setTitle($data['title']);
        $Todo->setDescription($data['description']);

        $em = $this->getDoctrine()->getManager();

        $em->merge($Todo);
        $em->flush();

        return $this->json(['data' => "Todo was changed"], $status = 200);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function deleteToDo(Request $request, UserRepository $users, TodoRepository $TodoRepository, $id): Response
    {
        $data = json_decode($request->getContent(), true);
        $data['username'] = $request->headers->get('php-auth-user');
        $data['password'] = $request->headers->get('php-auth-pw');
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

        $Todo = $TodoRepository->find($id);

        if ($Todo == null)
        {
            return $this->json(['error_message' => "This todo is not exist"], $status = 400);
        }

        if ($currentUser !== $Todo->getUser())
        {
            return $this->json(['error_message' => "This todo is not yours"], $status = 400);
        }

        $currentUser->removeTodo($Todo);

        $em = $this->getDoctrine()->getManager();
        $em->merge($currentUser);
        $em->remove($Todo);
        $em->flush();

        return $this->json(['data' => "Todo was deleted"], $status = 200);
    }
}
