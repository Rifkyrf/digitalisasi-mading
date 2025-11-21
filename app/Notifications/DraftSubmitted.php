<?php

// app/Notifications/DraftSubmitted.php

namespace App\Notifications;

use App\Models\Work;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DraftSubmitted extends Notification implements ShouldQueue // Opsional: untuk queue
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
                    ->subject('Draft Baru Dikirim - ' . $this->work->title)
                    ->greeting('Halo ' . $notifiable->name . ',')
                    ->line('Seorang siswa telah mengirimkan draft baru untuk ditinjau.')
                    ->line('Judul: ' . $this->work->title)
                    ->line('Penulis: ' . $this->work->user->name)
                    ->action('Lihat Draft', route('moderator.show', $this->work)) // Ganti dengan route yang benar jika perlu
                    ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    // Untuk notifikasi database
    public function toArray($notifiable)
    {
        return [
            'type' => 'draft_submitted',
            'work_id' => $this->work->id,
            'work_title' => $this->work->title,
            'user_name' => $this->work->user->name,
            'message' => 'Draft baru "' . $this->work->title . '" oleh ' . $this->work->user->name . ' menunggu verifikasi.',
            'url' => route('moderator.show', $this->work), // URL untuk mengarahkan saat klik notifikasi
        ];
    }
}