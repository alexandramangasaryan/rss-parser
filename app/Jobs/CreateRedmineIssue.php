<?php

namespace App\Jobs;

use App\Services\Redmine\Client;
use App\Services\Telegram\Telegram;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Log as LogModel;

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
        $redmine = new Client($this->redmineUrl, $this->apiKey);
        $redmine->setRedmineUrl((string)$this->redmineUrl);
        $redmine->setApiKey((string)$this->apiKey);

        $telegram = new Telegram($this->botToken);

        $subject = Str::replace('[[SKIP]]', 'Не указано', (isset($this->data['subject']) ? $this->data['subject'] : null));
        $description = Str::replace('[[SKIP]]', 'Не указано', (isset($this->data['description']) ? $this->data['description'] : null));
        $customFields = isset($this->data['custom_fields']) ? $this->data['custom_fields'] : [];
        try {
            $createdIssue = $redmine->getApi('issue')->create([
                'project_id' => $this->projectId,
                'subject' => $subject,
                'description' => $description,
                'tracker' => '2',
                'status' => '1',
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
                    $telegram->setAccessToken($this->botToken);
                    $telegramMessage = $telegram->sendMessage([
                        'chat_id' => $this->chatId,
                        'text' => $message,
                    ]);

                    LogModel::create([
                        'redmine_task_url' => $redmineTaskUrl,
                        'telegram_message_id' => $telegramMessage->messageId,
                        'sent_date' => Carbon::parse($telegramMessage->date)->toDateTimeString(),
                        'create_date' => Carbon::parse($createdIssue->created_on)->toDateTimeString(),
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception('Ошибка при отправке сообщений: ' . $e->getMessage(), 0);
                }

            } else return null;
        } catch (\Exception $e) {
            Storage::put('xml/' . date('Y-m-d_h-i-s') . '.xml', $redmine->getLastResponseBody());
            Log::error('REDMINE SERVICE: Ошибка при создании задачи: ' . $e->getMessage());
            throw new \Exception('REDMINE SERVICE: Ошибка при создании задачи: ' . $e->getMessage(), 0);
        }
    }
}
