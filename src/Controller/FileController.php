<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\User;
use App\Entity\File;
use App\Repository\UserRepository;

/**
 * @Route("/files")
 */
class FileController extends AbstractController
{
    /**
     * @Route("/", name="upload", methods={"POST"})
     */
    public function uploadFile(Request $request, UserRepository $users): Response
    {

        $fileData = $request->files->get('file');

        $username = $request->headers->get('username');
        $password = $request->headers->get('password');

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

            return $this->json(['data' => $file], $status = 200);
        }

        return $this->json(['data' => 'ok'], $status = 200);
    }
}
