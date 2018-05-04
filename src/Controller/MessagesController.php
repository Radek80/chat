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
use Symfony\Component\HttpFoundation\Session\Session;

if(!isset($_SESSION)) {
    $session = new Session();
    $session->start();
    $session->set('error', '');
}

class MessagesController extends AbstractController
{
    /**
     * @Route("/logout", name="logout")
     */
    public function logout(Session $session)
    {
        $session->remove('user');
        $session->remove('nickname');

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
    public function login($zmienna = null, Session $session)
    {
        if (($session->has('nickname')) && (strlen($session->get('nickname')) > 0)) {
            return $this->redirectToRoute('chat');
        }
        if ($zmienna != null) {
            if ($zmienna == "h6SJrXUiQtH00JJEfi5oYvbmz6nyU6iAVS3q5Igc")
                $session->set('user', 'moderator');
            if ($zmienna == "oQDUuIlLxUbWZiNa6iukYVCCapiGSg5XxvcGuyXa")
                $session->set('user', 'ekspert');
        }else{
            $session->set('user', 'uczestnik');
        }
        return $this->render('login.html.twig', [
            'user' => $session->get('user')
        ]);
    }

    /**
     * @Route("/chat", name="chat")
     */
    public function chat(Request $request, Session $session)
    {
        if ((($session->has('nickname')) == false) || (strlen($session->get('nickname')) < 1)) {
            $session->set('nickname', $request->request->get('nickname'));

            if ($session->get('user') == "moderator") {
                $session->set('nickname', $session->get('nickname') . "(moderator)");
            } elseif ($session->get('user') == "ekspert") {
                $session->set('nickname', $session->get('nickname') . "(ekspert)");
            }
        }


        if ($session->get('user') != "uczestnik") {
            $repository = $this->getDoctrine()->getRepository(Message::class);
            $newMessages = $repository->findBy(
                ['status' => "new"],
                ['input_date' => 'DESC']
            );
        }elseif ($session->get('user') == "uczestnik") {
            $repository = $this->getDoctrine()->getRepository(Message::class);
            $myMessages = $repository->findBy(
                ['status' => "new",
                'author' => $session->get('nickname')],
                ['input_date' => 'DESC']
            );
            $infoMessages = $repository->findBy(
                ['info' => ["accept", "decline"],
                    'author' => $session->get('nickname')],
                ['input_date' => 'DESC']
            );
        }
        $repository = $this->getDoctrine()->getRepository(Message::class);
        $acceptedMessages = $repository->findBy(
            ['status' => "accepted"],
            ['input_date' => 'DESC']
        );

        if (strlen($session->get('nickname')) < 1){
            return $this->render('nickerror.html.twig');
        }else {
            if ($session->get('user') != "uczestnik") {
                return $this->render('show.html.twig', [
                    'user' => $session->get('user'),
                    'nickname' => $session->get('nickname'),
                    'newMessages' => $newMessages,
                    'acceptedMessages' => $acceptedMessages,
                    'error' => $session->get('error')
                ]);
            }elseif ($session->get('user') == "uczestnik") {
                return $this->render('show.html.twig', [
                    'user' => $session->get('user'),
                    'nickname' => $session->get('nickname'),
                    'myMessages' => $myMessages,
                    'infoMessages' => $infoMessages,
                    'acceptedMessages' => $acceptedMessages,
                    'error' => $session->get('error')
                ]);
            }
        }
    }

    /**
     * @Route("/send", name="send")
     */
    public function send(Request $request, Session $session)
    {
        $session->set('error', '');
        $datenow = date("Y-m-d H:i:s");
        $author = $session->get('nickname');
        $description = $request->request->get('description');
        if ($session->get('user') == "uczestnik")
            $status="new";
        else if (($session->get('user') == "moderator") || ($session->get('user') == "ekspert"))
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
            $session->set('error', 'Podaj treść wiadomości!');
            return $this->render('senderror.html.twig', [
                'error' => $session->get('error')
            ]);
        }
    }
}