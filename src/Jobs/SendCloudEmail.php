<?php

namespace JoseChan\Email\Utils\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Message;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use JoseChan\Email\DataSet\Models\EmailQueue;
use Naux\Mail\SendCloudTemplate;

/**
 * 邮件消费者
 * Class SendCloudEmail
 * @package App\Jobs
 */
class SendCloudEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $message;

    /**
     * SendCloudEmail constructor.
     * @param array $message
     */
    public function __construct(array $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $queue_id = $this->message['queue_id'];
        /** @var EmailQueue $queue */
        $queue = EmailQueue::query()->find($queue_id);

        if (!$queue || !$queue->exists) {
            return true;
        }

        $from = ["address" => $queue->mission->from_email, "name" => $queue->mission->from_email];
        $subject = $queue->mission->subject;
        $to = $queue->to_email;
        $view = file_get_contents(config("filesystems.disks.admin.root") . DIRECTORY_SEPARATOR . $queue->mission->template->view);

        try {
            Mail::raw($view, function (Message $message) use ($from, $subject, $to) {
                $message->from($from['address'], $from['name']);
                $message->subject($subject);
                $message->to($to);
            });

            //发送成功
            $queue->status = 1;
            $queue->save();
        } catch (\Exception $e) {
            //发送失败
            $queue->status = 2;
            $queue->err_msg = $e->getMessage();
            $queue->save();
        }

    }
}
