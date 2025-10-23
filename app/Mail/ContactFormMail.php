<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    // Définition de l’enveloppe (expéditeur, sujet, etc.)
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Message de contact - ' . ($this->content['sujet'] ?? 'Sans sujet'),
                 from: $this->content['email'] ?? 'noreply@travail.gouv.bj' // ✅ juste l'email

        );
    }

    // Définition du contenu (vue + variables)
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact_form',
            with: ['content' => $this->content] // ✅ ATTENTION : doit être un tableau
        );
    }
}
