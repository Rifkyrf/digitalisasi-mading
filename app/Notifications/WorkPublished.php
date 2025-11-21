<?php

// app/Notifications/WorkPublished.php

namespace App\Notifications;

use App\Models\Work;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkPublished extends Notification implements ShouldQueue // Opsional: untuk queue
{
    use Queueable;

    public $work;

    public function __construct(Work $work)
    {
        $this->work = $work;
    }

    public function via($notifiable)
    {
        // Kirim via database dan email
        return ['database', 'mail'];
    }

    // Untuk notifikasi email
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Karya Anda Telah Dipublikasikan - ' . $this->work->title)
                    ->greeting('Selamat ' . $notifiable->name . '!')
                    ->line('Karya Anda yang berjudul "' . $this->work->title . '" telah berhasil dipublikasikan.')
                    ->action('Lihat Karya', route('work.show', $this->work)) // Ganti dengan route yang benar jika perlu
                    ->line('Terima kasih atas kontribusinya!');
    }

    // Untuk notifikasi database
    public function toArray($notifiable)
    {
        return [
            'type' => 'work_published',
            'work_id' => $this->work->id,
            'work_title' => $this->work->title,
            'message' => 'Karya "' . $this->work->title . '" telah berhasil dipublikasikan.',
            'url' => route('work.show', $this->work), // URL untuk mengarahkan saat klik notifikasi
        ];
    }
}