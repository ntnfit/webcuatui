<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\blogs as Post;
use App\Exceptions\CannotSendEmail;
class BlogPublished extends Mailable
{
    use Queueable, SerializesModels;
    public $groupedPosts;
    public function __construct(private Post $post, private string $toEamil = '')
    {
    }
    public function envelope(): Envelope
    {
        if ($this->post->isNotPublished()) {
            throw CannotSendEmail::postNotPublished();
        }
        return new Envelope(
            to: $this->toEamil,
            subject: 'Bài viết mới vừa được xuất bản'
        );
    }
    public function content(): Content
    {
        $otherPosts = Post::where('id', '!=', $this->post->id)
            ->latest()
            ->take(4)
            ->get();
        $this->groupedPosts = $otherPosts->chunk(2);
        return new Content(
            view: 'mail.newsletter_new_post',
            with: ['post' => $this->post,'groupedPosts' => $this->groupedPosts]);
    }
}
