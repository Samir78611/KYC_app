<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $role;
    public $invitationLink;

     /**
     * Create a new message instance.
     *
     * @param string $name
     * @param string $email
     * @param string $role
     */
    public function __construct($name, $email, $role)
    {
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;

        // Generate an invitation link (customize the URL as needed)
        $this->invitationLink = url('/admin/setup-account?email=' . urlencode($this->email));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.admin_invitation')
                    ->subject('You have been invited to join as an Admin')
                    ->with([
                        'name' => $this->name,
                        'role' => $this->role,
                        'invitationLink' => $this->invitationLink,
                    ]);
    }
}
