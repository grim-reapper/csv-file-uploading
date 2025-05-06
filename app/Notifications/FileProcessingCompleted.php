<?php

namespace App\Notifications;

use App\Models\FileUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FileProcessingCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected FileUpload $fileUpload
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('File Processing Completed')
            ->line('Your file ' . $this->fileUpload->original_filename . ' has been processed successfully.')
            ->line('Total rows processed: ' . $this->fileUpload->processed_rows)
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'file_upload_id' => $this->fileUpload->id,
            'filename' => $this->fileUpload->original_filename,
            'status' => $this->fileUpload->status,
            'processed_rows' => $this->fileUpload->processed_rows,
            'total_rows' => $this->fileUpload->total_rows,
        ];
    }
}