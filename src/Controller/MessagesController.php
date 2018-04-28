<?php
/**
 * Created by PhpStorm.
 * User: Łysy
 * Date: 2018-04-26
 * Time: 20:12
 */

namespace App\Controller;

use App\Entity\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
if(!isset($_SESSION)) {
    session_start();
    $_SESSION['error'] = "";
}

class MessagesController extends AbstractController
{
    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        unset($_SESSION['user']);
        unset($_SESSION['nickname']);

        return $this->render('logout.html.twig');
    }

    /**
     * @Route("/accept", name="accept")
     */
    public function accept(Request $request)
    {
        $id = $request->request->get('id');
        $datenow = date("Y-m-d H:i:s");
        $entityManager = $this->getDoctrine()->getManager();
        $updatedMessage = $entityManager->getRepository(Message::class)->find($id);

        $updatedMessage->setStatus('accepted');
        $updatedMessage->setInfo('accept');
        $updatedMessage->setInputDate($datenow);
        $entityManager->flush();

        return $this->redirectToRoute('chat');
    }

    /**
     * @Route("/decline", name="decline")
     */
    public function decline(Request $request)
    {
        $id = $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $updatedMessage = $entityManager->getRepository(Message::class)->find($id);

        $updatedMessage->setStatus('declined');
        $updatedMessage->setInfo('decline');
        $entityManager->flush();

        return $this->redirectToRoute('chat');
    }

    /**
     * @Route("/readDeclined", name="readDeclined")
     */
    public function readDeclined(Request $request)
    {
        $id = $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $deletedMessage = $entityManager->getRepository(Message::class)->find($id);

        $entityManager->remove($deletedMessage);
        $entityManager->flush();

        return $this->redirectToRoute('chat');
    }

    /**
     * @Route("/readAccepted", name="readAccepted")
     */
    public function readAccepted(Request $request)
    {
        $id = $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $updatedMessage = $entityManager->getRepository(Message::class)->find($id);

        $updatedMessage->setInfo(null);
        $entityManager->flush();

        return $this->redirectToRoute('chat');
    }

    /**
     * @Route("/login", name="login")
     * @Route ("/login/{zmienna}")
     */
    public function login($zmienna = null)
    {
        if ((isset($_SESSION['nickname'])) && (strlen($_SESSION['nickname']) > 0)) {
            return $this->redirectToRoute('chat');
        }
        if ($zmienna != null) {
            if ($zmienna == "h6SJrXUiQtH00JJEfi5oYvbmz6nyU6iAVS3q5Igc")
                $_SESSION['user'] = "moderator";
            if ($zmienna == "oQDUuIlLxUbWZiNa6iukYVCCapiGSg5XxvcGuyXa")
                $_SESSION['user'] = "ekspert";
        }else{
            $_SESSION['user'] = "uczestnik";
        }
        return $this->render('login.html.twig', [
            'user' => $_SESSION['user']
        ]);
    }

    /**
     * @Route("/chat", name="chat")
     */
    public function chat(Request $request)
    {
        if ((!isset($_SESSION['nickname'])) || (strlen($_SESSION['nickname']) < 1)) {
            $_SESSION['nickname'] = $request->request->get('nickname');

            if ($_SESSION['user'] == "moderator") {
                $_SESSION['nickname'] = ($_SESSION['nickname'] . "(moderator)");
            } elseif ($_SESSION['user'] == "ekspert") {
                $_SESSION['nickname'] = ($_SESSION['nickname'] . "(ekspert)");
            }
        }


        if ($_SESSION['user'] != "uczestnik") {
            $repository = $this->getDoctrine()->getRepository(Message::class);
            $newMessages = $repository->findBy(
                ['status' => "new"],
                ['input_date' => 'DESC']
            );
        }elseif ($_SESSION['user'] == "uczestnik") {
            $repository = $this->getDoctrine()->getRepository(Message::class);
            $myMessages = $repository->findBy(
                ['status' => "new",
                'author' => $_SESSION['nickname']],
                ['input_date' => 'DESC']
            );
            $infoMessages = $repository->findBy(
                ['info' => ["accept", "decline"],
                    'author' => $_SESSION['nickname']],
                ['input_date' => 'DESC']
            );
        }
        $repository = $this->getDoctrine()->getRepository(Message::class);
        $acceptedMessages = $repository->findBy(
            ['status' => "accepted"],
            ['input_date' => 'DESC']
        );

        if (strlen($_SESSION['nickname']) < 1){
            return $this->render('nickerror.html.twig');
        }else {
            if ($_SESSION['user'] != "uczestnik") {
                return $this->render('show.html.twig', [
                    'user' => $_SESSION['user'],
                    'nickname' => $_SESSION['nickname'],
                    'newMessages' => $newMessages,
                    'acceptedMessages' => $acceptedMessages,
                    'error' => $_SESSION['error']
                ]);
            }elseif ($_SESSION['user'] == "uczestnik") {
                return $this->render('show.html.twig', [
                    'user' => $_SESSION['user'],
                    'nickname' => $_SESSION['nickname'],
                    'myMessages' => $myMessages,
                    'infoMessages' => $infoMessages,
                    'acceptedMessages' => $acceptedMessages,
                    'error' => $_SESSION['error']
                ]);
            }
        }
    }

    /**
     * @Route("/send", name="send")
     */
    public function send(Request $request)
    {
        $_SESSION['error'] = "";
        $datenow = date("Y-m-d H:i:s");
        $author = $_SESSION['nickname'];
        $description = $request->request->get('description');
        if ($_SESSION['user'] == "uczestnik")
            $status="new";
        else if (($_SESSION['user'] == "moderator") || ($_SESSION['user'] == "ekspert"))
            $status="accepted";

        $entityManager = $this->getDoctrine()->getManager();

        if (strlen($description) > 1) {
            $message = new Message();
            $message->setDescription($description);
            $message->setAuthor($author);
            $message->setInputDate($datenow);
            $message->setStatus($status);

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('chat');
        }else {
            $_SESSION['error'] = "Podaj treść wiadomości!";
            return $this->render('senderror.html.twig', [
                'error' => $_SESSION['error']
            ]);
        }
    }
}