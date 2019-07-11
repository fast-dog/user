<?php

namespace FastDog\User\Jobs;

use FastDog\Config\Models\Emails;
use FastDog\User\Models\UserEmailSubscribe;
use FastDog\User\Models\UserMailing;
use FastDog\User\Models\UserMailingProcess;
use FastDog\User\Models\UserMailingReport;
use FastDog\User\Models\UserMailingTemplates;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class SendMailing
 * @package FastDog\User\Jobs
 */
class SendMailing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var UserMailing $process
     */
    protected $process;

    /**
     * Create a new job instance.
     *
     * @param UserMailingProcess $process
     */
    public function __construct(UserMailingProcess $process)
    {
        $this->process = $process;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if ($this->process) {
            /** @var UserMailing $mailing */
            $mailing = $this->process->mailing;
            $template = UserMailingTemplates::where([
                'id' => $mailing->getTemplateId(),
            ])->first();
            $error = false;
            UserEmailSubscribe::where([
                UserEmailSubscribe::STATE => UserEmailSubscribe::STATE_PUBLISHED,
                UserEmailSubscribe::SITE_ID => $mailing->{UserMailing::SITE_ID},
            ])->get()->each(function (UserEmailSubscribe $item) use ($mailing, $template, &$error) {
                try {
                    if ($item->{UserEmailSubscribe::EMAIL}) {
                        $params = [
                            'TEXT' => $mailing->{UserMailing::TEXT},
                            'TITLE' => $mailing->{UserMailing::NAME},
                            'subject' => $mailing->{UserMailing::SUBJECT},
                            'to' => $item->{UserEmailSubscribe::EMAIL},
                        ];

                        Emails::send($template, $params);

                        UserMailingReport::create([
                            UserMailingReport::USER_ID => $item->id,
                            UserMailingReport::MAILING_ID => $mailing->id,
                            UserMailingReport::PROCESS_ID => $this->process->id,
                            UserMailingReport::TEMPLATE_ID => $mailing->getTemplateId(),
                            UserMailingReport::DATA => json_encode($params),
                        ]);
                    }
                } catch (\Exception $e) {
                    $error = true;
                    $this->process->update([
                        UserMailingProcess::STATE => UserMailingProcess::STATE_ERROR,
                        UserMailingProcess::DATA => json_encode($e),
                    ]);
                }
            });

            if ($error === false) {
                $this->process->update([
                    UserMailingProcess::STATE => UserMailingProcess::STATE_FINISH,
                ]);
            }
        }
    }
}
