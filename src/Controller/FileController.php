<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Entity\User;
use App\Entity\File;
use App\Repository\UserRepository;
use App\Repository\FileRepository;

/**
 * @Route("/files")
 */
class FileController extends AbstractController
{
    /**
     * @Route("/", name="upload", methods={"POST"})
     */
    public function uploadFile(Request $request, UserRepository $users, FileRepository $fileRepository): Response
    {
        $fileData = $request->files->get('file');



        $username = $request->headers->get('php-auth-user');
        $password = $request->headers->get('php-auth-pw');

        if (!isset($username))
        {
            return $this->json(['error_message' => "Username not specified"], $status = 400);
        }
        if (!isset($password))
        {
            return $this->json(['error_message' => "Password not specified"], $status = 400);
        }

        if ($username == null)
        {
            return $this->json(['error_message' => "Username empty"], $status = 400);
        }
        if ($password == null)
        {
            return $this->json(['error_message' => "Password empty"], $status = 400);
        }

        $currentUser = $users->findOneByUsername($username);

        if($currentUser == null || $currentUser->getPassword() !== $password) {
            return $this->json(['error_message' => "Login or password incorrect"], $status = 400);
        }

        $files_directory = $this->getParameter('files_directory');

        if ($fileData)
        {
            if ($fileRepository->findOneBy(['fileName' => $fileData->getClientOriginalName()]) != null)
            {
                return $this->json(['error_message' => "File with this name already exist. Please rename file and try again"], $status = 400);
            }
            $filename = md5(uniqid()) . '.' . $fileData->guessClientExtension();

            $fileData->move(
                $files_directory,
                $filename
            );

            $file = new File();
            $file->setFileName($fileData->getClientOriginalName());
            $file->setUploadedName($filename);
            $file->setUser($currentUser);
            $file->setSize(filesize($files_directory . '/' . $filename));
            $currentUser->addFile($file);
            $em = $this->getDoctrine()->getManager();
            $em->persist($file);
            $em->persist($currentUser);
            $em->flush();

            return $this->json(['data' => "Your file has been uploaded"], $status = 200);
        }

        return $this->json(['error_message' => "No file selected"], $status = 400);

    }

    /**
     * @Route("/", name="all_files", methods={"GET"})
     */
    public function getFiles(Request $request, FileRepository $fileRepository, UserRepository $users): Response
    {
        $username = $request->headers->get('php-auth-user');
        $password = $request->headers->get('php-auth-pw');

        if (!isset($username))
        {
            return $this->json(['error_message' => "Username not specified"], $status = 400);
        }
        if (!isset($password))
        {
            return $this->json(['error_message' => "Password not specified"], $status = 400);
        }

        if ($username == null)
        {
            return $this->json(['error_message' => "Username empty"], $status = 400);
        }
        if ($password == null)
        {
            return $this->json(['error_message' => "Password empty"], $status = 400);
        }

        $currentUser = $users->findOneByUsername($username);

        if($currentUser == null || $currentUser->getPassword() !== $password) {
            return $this->json(['error_message' => "Login or password incorrect"], $status = 400);
        }

        $files = $fileRepository->findBy([
            "user" => $currentUser
        ]);

        if ($files == null)
        {
            return $this->json(['error_message' => "No files with this name"], $status = 200);
        }

        $result = null;

        foreach ($files as $file) {
            $array = [
                "id" => $file->getId(),
                "filename" => $file->getFileName(),
                "size_byte" => $file->getSize()
            ];
            $result[] = $array;
        }

        if ($result == null)
        {
            return $this->json("Not okay", $status = 500);
        }
        else
            return $this->json($result, $status = 200);
    }

    /**
     * @Route("/{name}", name="file", methods={"GET"})
     */
    public function getFile(Request $request, FileRepository $fileRepository, UserRepository $users, $name): Response
    {
        $username = $request->headers->get('php-auth-user');
        $password = $request->headers->get('php-auth-pw');

        if (!isset($username))
        {
            return $this->json(['error_message' => "Username not specified"], $status = 400);
        }
        if (!isset($password))
        {
            return $this->json(['error_message' => "Password not specified"], $status = 400);
        }

        if ($username == null)
        {
            return $this->json(['error_message' => "Username empty"], $status = 400);
        }
        if ($password == null)
        {
            return $this->json(['error_message' => "Password empty"], $status = 400);
        }

        $currentUser = $users->findOneByUsername($username);

        if($currentUser == null || $currentUser->getPassword() !== $password) {
            return $this->json(['error_message' => "Login or password incorrect"], $status = 400);
        }

        $file = $fileRepository->findOneBy(['fileName' => $name]);

        if ($file == null)
        {
            return $this->json(['error_message' => "No files with this name"], $status = 200);
        }

        if ($currentUser !== $file->getUser())
        {
            return $this->json(['error_message' => "This file is not yours"], $status = 400);
        }

        $files_directory = $this->getParameter('files_directory');

        return $this->dowloadResult($files_directory . '/' . $file->getUploadedName());
    }

    private function dowloadResult($filepath) : BinaryFileResponse
    {
        return new BinaryFileResponse($filepath);
    }

    /**
     * @Route("/{name}", name="delete_file", methods={"DELETE"})
     */
    public function deleteFile(Request $request, FileRepository $fileRepository, UserRepository $users, $name): Response
    {
        $username = $request->headers->get('php-auth-user');
        $password = $request->headers->get('php-auth-pw');
        if (!isset($username))
        {
            return $this->json(['error_message' => "Username not specified"], $status = 400);

        }
        if (!isset($password))
        {
            return $this->json(['error_message' => "Password not specified"], $status = 400);

        }

        if ($username == null)
        {
            return $this->json(['error_message' => "Username empty"], $status = 400);
        }
        if ($password == null)
        {
            return $this->json(['error_message' => "Password empty"], $status = 400);
        }

        $currentUser = $users->findOneByUsername($username);

        if($currentUser == null || $currentUser->getPassword() !== $password) {
            return $this->json(['error_message' => "Login or password incorrect"], $status = 400);
        }

        $file = $fileRepository->findOneBy(['fileName' => $name]);

        if ($file == null)
        {
            return $this->json(['error_message' => "No files with this name"], $status = 200);
        }

        if ($currentUser !== $file->getUser())
        {
            return $this->json(['error_message' => "This file is not yours"], $status = 400);
        }

        $currentUser->removeFile($file);

        $em = $this->getDoctrine()->getManager();
        $em->merge($currentUser);
        $em->remove($file);
        $em->flush();

        $files_directory = $this->getParameter('files_directory');

        unlink($files_directory . '/' . $file->getUploadedName());

        return $this->json(['data' => "File was deleted"], $status = 200);
    }

}
