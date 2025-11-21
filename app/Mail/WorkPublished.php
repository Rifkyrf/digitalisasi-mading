<?php

namespace App\Mail;

use App\Models\Work;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkPublished extends Mailable
{
    use Queueable, SerializesModels;

    public $work;

    public function __construct(Work $work)
    {
        $this->work = $work;
    }

    public function build()
    {
        return $this->subject('Karya Anda Telah Dipublikasikan - ' . $this->work->title)
                    ->view('mails.work-published')
                    ->with([
                        'work' => $this->work,
                        'author' => $this->work->user->name,
                    ]);
    }
}