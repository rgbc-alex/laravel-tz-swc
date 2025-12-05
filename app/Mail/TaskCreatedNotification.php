<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Уведомление о создании новой задачи
 */
class TaskCreatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Task
     */
    public Task $task;

    /**
     * Create a new message instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новая задача создана',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tasks.created',
            with: [
                'task' => $this->task,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
