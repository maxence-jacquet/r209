<?php
// src/Controller/ContactController.php
namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $contact = new Contact();
        $form = $this->createForm(ContactForm::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde en base
            $contact->setCreateAt(new \DateTimeImmutable());
            $em->persist($contact);
            $em->flush();

            // Envoi du mail
            $email = (new Email())
                ->from('maxence.1686@outlook.com')
                ->to('maxence.1686@gmail.com') // Change par ton adresse
                ->subject($contact->getSujet())
                ->text(
                    "Nom : {$contact->getNom()}\n" .
                    "Prénom : {$contact->getPrenom()}\n" .
                    "Email : {$contact->getEmail()}\n" .
                    "Sujet : {$contact->getSujet()}\n\n" .
                    "Message :\n{$contact->getMessage()}"
                );

            $mailer->send($email);

            $this->addFlash('success', 'Message envoyé avec succès.');
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
