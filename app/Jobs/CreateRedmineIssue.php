<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Redmine\Client\NativeCurlClient;
use Telegram\Bot\Api;

class CreateRedmineIssue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $redmineUrl;
    protected $apiKey;
    protected $projectId;
    protected $botToken;
    protected $chatId;
    protected $data;
    /**
     * Create a new job instance.
     */
    public function __construct($redmineUrl, $apiKey, $projectId, $botToken, $chatId, array $data)
    {
        $this->redmineUrl = $redmineUrl;
        $this->apiKey = $apiKey;
        $this->projectId = $projectId;
        $this->botToken = $botToken;
        $this->chatId = $chatId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle()
    {
        $redmine = new NativeCurlClient($this->redmineUrl, $this->apiKey);

        $subject = Str::replace('[[SKIP]]', 'Не указано', (isset($this->data['subject']) ? $this->data['subject'] : null));
        $description = Str::replace('[[SKIP]]', 'Не указано', (isset($this->data['description']) ? $this->data['description'] : null));
        $customFields = isset($this->data['custom_fields']) ? $this->data['custom_fields'] : [];

        try {
            $createdIssue = $redmine->getApi('issue')->create([
                'project_id' => $this->projectId,
                'subject' => $subject,
                'description' => $description,
                'custom_fields' => $customFields,
            ]);

            if (!empty($createdIssue->error)) {
                $errors = implode(PHP_EOL, (array)$createdIssue->error);
                throw new \Exception('Redmine возвратил список ошибок: ' . PHP_EOL . $errors);
            }

            if ((int)$createdIssue->id !== null) {
                $redmineTaskId = $createdIssue->id;
                $redmineTaskUrl = $this->redmineUrl . '/issues/' . $redmineTaskId;

                $message = $subject."\n\n";
                $message .= "Номер задачи: " . $redmineTaskId . "\n";
                $message .= "URL задачи: " . $redmineTaskUrl;
                try {
                    $telegram = new Api($this->botToken);
                    $telegramMessage = $telegram->sendMessage([
                        'chat_id' => $this->chatId,
                        'text' => $message,
                    ]);

                    Log::create([
                        'redmine_task_url' => $redmineTaskUrl,
                        'telegram_message_id' => $telegramMessage->messageId,
                        'create_date' => $createdIssue->created_on,
                        'sent_date' => $telegramMessage->date
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception('Ошибка при отправке сообщений: ' . $e->getMessage());
                }

            } else return null;
        } catch (\Exception $e) {
            Storage::put('xml/' . date('Y-m-d_h-i-s') . '.xml', $redmine->getLastResponseBody());
            Log::error($e);
            throw new \Exception($e, 'REDMINE SERVICE: Ошибка при создании задачи: ' . $e->getMessage());
        }
    }
}
