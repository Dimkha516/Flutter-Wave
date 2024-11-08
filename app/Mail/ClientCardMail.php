<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Storage;

class ClientCardMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre Carte Wave',
        );
    }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        // Chemin de la carte générée
        $filePath = Storage::path($this->client->qr_code);
        // return $this->subject('Votre carte client avec QR code')
        //     ->from('dimkha516@example.com', 'Wave')
        //     ->to($this->client->email)
        //     ->html('<p>Bonjour ' . $this->client->nom . ' ' . $this->client->prenom . ',</p><p>Veuillez trouver ci-joint votre carte client avec le QR code.</p>')
        //     ->attach($filePath, [
        //         'as' => 'ClientCard.png',
        //         'mime' => 'image/png',
        //     ]);
        
        return $this->subject('Votre carte client avec QR code')
            ->view('emails.client_card') // Vue du mail
            ->with([
                'client' => $this->client,
            ])
            ->attach($filePath, [
                'as' => 'ClientCard.png',
                'mime' => 'image/png',
            ]);
    }
}
