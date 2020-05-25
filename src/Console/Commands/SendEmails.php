<?php

namespace JoseChan\Email\Utils\Console\Commands;

use Carbon\Carbon;
use JoseChan\Email\DataSet\Models\EmailQueue;
use JoseChan\Email\Utils\Jobs\SendCloudEmail;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * 邮件消息生产者
 * Class SendEmails
 * @package App\Console\Commands
 */
class SendEmails extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send emails';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //获取所有待发送任务（今天内当前时间之前还没发送的）
        $list = EmailQueue::fetchPendingList();

        $list->map(function (EmailQueue $item) {
            $message = [
                "queue_id" => $item->id
            ];
            $job = new SendCloudEmail($message);
            $job->onConnection("emails");
            $job->onQueue("emails");

            $this->dispatch($job);
        });
    }
}
