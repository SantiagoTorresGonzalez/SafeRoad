<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PremiumContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SafeRoad SC – Tu acceso al módulo de Predicción con IA',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.premium_contact',
            with: ['user' => $this->user],
        );
    }
}
