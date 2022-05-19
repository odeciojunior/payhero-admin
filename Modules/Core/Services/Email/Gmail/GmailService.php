<?php

namespace Modules\Core\Services\Email\Gmail;

use Carbon\Carbon;
use Exception;
use Google_Service_Gmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleContestation;

class GmailService
{
    private $client;
    private $storageDrive;

    public function __construct()
    {
        $conn = new GmailConnection();
        $this->client = $conn->get_client();
        $this->storageDrive = Storage::disk('local');
    }

    public function clearFolder(): void
    {
        $hasFiles = $this->storageDrive->allFiles('contestation/');
        foreach ($hasFiles as $fileToRemove) {
            $this->storageDrive->delete($fileToRemove);
        }
    }

    public function getAttachments($limit = 200, $date_after = null, $recursive = false): array
    {
        $this->clearFolder();

        $service = new Google_Service_Gmail($this->client);
        $userId = 'me';

        if (!$date_after) {
            $date_after = Carbon::today()->format('Y/m/d');
        }


        $maxResults = $limit;
        $pageToken = null;
        $q = 'from:(pedido.documento@getnet.com.br) after:' . $date_after;

        //CHAT - SENT - INBOX - IMPORTANT - TRASH - DRAFT - SPAM - CATEGORY_FORUMS - CATEGORY_UPDATES - CATEGORY_PERSONAL - CATEGORY_PROMOTIONS - CATEGORY_SOCIAL - STARRED - UNREAD
        $labelIds = array('INBOX');

        $includeSpamTrash = false;
        $alt = 'json';

        $messages = array();

        $opt_params = [
            'maxResults' => $maxResults,
            'pageToken' => $pageToken,
            'q' => $q,
            'labelIds' => $labelIds,
            'includeSpamTrash' => $includeSpamTrash,
            'alt' => $alt
        ];

        $paths = [];

        if ($recursive == true) {
            do {
                try {
                    if ($pageToken) {
                        $opt_params['pageToken'] = $pageToken;
                    }

                    $messagesResponse = $service->users_messages->listUsersMessages($userId, $opt_params);

                    if ($messagesResponse->getMessages()) {
                        $messages = array_merge($messages, $messagesResponse->getMessages());
                        $pageToken = $messagesResponse->getNextPageToken();
                    }
                } catch (Exception $e) {
                    print 'An error occurred: ' . $e->getMessage();
                }
            } while ($pageToken);
        } else {
            $messages = $service->users_messages->listUsersMessages($userId, $opt_params);
        }

        foreach ($messages as $message) {
            $msg = $service->users_messages->get($userId, $message->getId());
            $parts = $msg->getPayload()->getParts();


            if (count($parts) > 0) {
                $attachmentId = $parts[1]->getBody()->getAttachmentId();
            } else {
                $attachmentId = $msg->getPayload()->getBody()->getAttachmentId();
            }

            $data = $service->users_messages_attachments->get(
                'chargebackgetnet@gmail.com',
                $message->getId(),
                $attachmentId
            );

            $file_path = 'contestation/' . str_random(30) . time() . '.xlsx';
            $this->storageDrive->put($file_path, $this->base64url_decode(utf8_encode($data->data)), 'public');
            $paths[] = $this->storageDrive->url($file_path);

            //
            //
            //            $file_name = str_random(30) . time() . '.xlsx';
            //
            //            $s3Drive->put(
            //                'uploads/public/contestation/' . $file_name,
            //                $this->base64url_decode(utf8_encode($data->data)),
            //                'public'
            //            );
            //
            //            $paths[] = $s3Drive->url(
            //                'uploads/public/contestation/' . $file_name
            //            );

        }

        return $paths;
    }

    public function getMessages($limit = 200, $recursive = false)
    {
        $service = new Google_Service_Gmail($this->client);
        $userId = 'me';
        $maxResults = $limit;
        $pageToken = null;
        $q = 'monitoria_marketplace@getnet.com.br';
        $labelIds = array('INBOX'); //CHAT - SENT - INBOX - IMPORTANT - TRASH - DRAFT - SPAM - CATEGORY_FORUMS - CATEGORY_UPDATES - CATEGORY_PERSONAL - CATEGORY_PROMOTIONS - CATEGORY_SOCIAL - STARRED - UNREAD
        $includeSpamTrash = false;
        $alt = 'json';

        $messages = array();
        $opt_param = array();

        $opt_params = [
            'maxResults' => $maxResults,
            'pageToken' => $pageToken,
            'q' => $q,
            'labelIds' => $labelIds,
            'includeSpamTrash' => $includeSpamTrash,
            'alt' => $alt
        ];

        if ($recursive == true) {
            do {
                try {
                    if ($pageToken) {
                        $opt_params['pageToken'] = $pageToken;
                    }

                    $messagesResponse = $service->users_messages->listUsersMessages($userId, $opt_params);

                    if ($messagesResponse->getMessages()) {
                        $messages = array_merge($messages, $messagesResponse->getMessages());
                        $pageToken = $messagesResponse->getNextPageToken();
                    }
                } catch (Exception $e) {
                    print 'An error occurred: ' . $e->getMessage();
                }
            } while ($pageToken);
        } else {
            $messages = $service->users_messages->listUsersMessages($userId, $opt_params);
        }

        $decode_msg = array();

        foreach ($messages as $message) {
            //print " \n " . "Message with ID: " . $message->getId() . " \n ";
            $msg = $service->users_messages->get($userId, $message->getId());
            $parts = $msg->getPayload()->getParts();
            if (count($parts) > 0) {
                $data = $parts[1]->getBody()->getData();
            } else {
                $data = $msg->getPayload()->getBody()->getData();
            }
            $out = str_replace("-", "+", $data);
            $out = str_replace("_", "/", $out);
            $decode_msg[] = base64_decode($out);
        }
        return $decode_msg;
    }

    public function getMessagesWithQuery($limit = 200, $query, $recursive = false)
    {
        $service = new Google_Service_Gmail($this->client);
        $userId = 'me';
        $maxResults = $limit;
        $pageToken = null;
        $q = $query;
        $labelIds = array('INBOX'); //CHAT - SENT - INBOX - IMPORTANT - TRASH - DRAFT - SPAM - CATEGORY_FORUMS - CATEGORY_UPDATES - CATEGORY_PERSONAL - CATEGORY_PROMOTIONS - CATEGORY_SOCIAL - STARRED - UNREAD
        $includeSpamTrash = false;
        $alt = 'json';

        $messages = array();
        $opt_param = array();

        $opt_params = [
            'maxResults' => $maxResults,
            'pageToken' => $pageToken,
            'q' => $q,
            'labelIds' => $labelIds,
            'includeSpamTrash' => $includeSpamTrash,
            'alt' => $alt
        ];

        if ($recursive == true) {
            do {
                try {
                    if ($pageToken) {
                        $opt_params['pageToken'] = $pageToken;
                    }

                    $messagesResponse = $service->users_messages->listUsersMessages($userId, $opt_params);

                    if ($messagesResponse->getMessages()) {
                        $messages = array_merge($messages, $messagesResponse->getMessages());
                        $pageToken = $messagesResponse->getNextPageToken();
                    }
                } catch (Exception $e) {
                    print 'An error occurred: ' . $e->getMessage();
                }
            } while ($pageToken);
        } else {
            $messages = $service->users_messages->listUsersMessages($userId, $opt_params);
        }

        $decode_msg = array();
        $decode_date = '';

        foreach ($messages as $message) {
            //print " \n " . "Message with ID: " . $message->getId() . " \n ";


            $msg = $service->users_messages->get($userId, $message->getId());
            $parts = $msg->getPayload()->getParts();

            $headers = $msg->getPayload()->getHeaders();

            foreach ($headers as $header) {
                if ($header->name == 'Date') {
                    $decode_date = Carbon::parse($header->value)->format('Y-m-d');
                }
            }


            if (count($parts) > 0) {
                $data = $parts[1]->getBody()->getData();
            } else {
                $data = $msg->getPayload()->getBody()->getData();
            }

            $out = str_replace("-", "+", $data);
            $out = str_replace("_", "/", $out);

            $decode_msg[] = [
                'text' => base64_decode($out),
                'date' => $decode_date
            ];
        }

        return $decode_msg;
    }

    public function getDataFromEmail($txt, $tag)
    {
        $offset = 0;
        $start_tag = "<" . $tag;
        $end_tag = "</" . $tag . ">";
        $arr = array();
        do {
            $pos = strpos($txt, $start_tag, $offset);
            if ($pos) {
                $str_pos = strpos($txt, ">", $pos) + 1;
                $end_pos = strpos($txt, $end_tag, $str_pos);
                $len = $end_pos - $str_pos;
                $f_text = substr($txt, $str_pos, $len);

                $arr[] = $f_text;
                $offset = $end_pos;
            }
        } while ($pos);

        return $arr;
    }

    public function getValueFromEmail($string)
    {
        $arrayRemove = ["strong", "<", ">", "/", "\r", "\n", " "];
        $string = explode(":", $string);
        $string = $string[1];
        foreach ($arrayRemove as $value) {
            $string = str_replace($value, "", $string);
        }
        return $string;
    }

    function base64url_decode($data, $strict = false)
    {
        // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
        $b64 = strtr($data, '-_', '+/');

        // Decode Base64 string and return the original data
        return base64_decode($b64, $strict);
    }

    public function readLabels()
    {
        $service = new Google_Service_Gmail($this->client);

        // Print the labels in the user's account.
        $user = 'me';
        $results = $service->users_labels->listUsersLabels($user);

        if (count($results->getLabels()) == 0) {
            print "No labels found.\n";
        } else {
            print "Labels:\n";
            foreach ($results->getLabels() as $label) {
                printf("- %s\n", $label->getName());
                //print_r("<pre>". var_export($label, true) . "</pre>");
            }
        }

        return true;
    }

    public function syncContestations()
    {
        $emailMessages = $this->getMessages();

        foreach ($emailMessages as $emailMessage) {
            $data = [];

            if (!empty($emailMessage)) {
                $arr = $this->getDataFromEmail($emailMessage, "p");
                $nsuTerminal = $this->getValueFromEmail($arr[4]);

                $data = [
                    'valor_chargeback' => $this->getValueFromEmail($arr[2]),
                    'nsu_transacao' => $this->getValueFromEmail($arr[3]),
                    'nsu_terminal' => $nsuTerminal,
                    'numero_chargeback' => $this->getValueFromEmail($arr[5]),
                    'status_transacao' => $this->getValueFromEmail($arr[6]),
                ];


                $sale = Sale::whereHas(
                    'saleGatewayRequests',
                    function ($query) use ($nsuTerminal) {
                        $query->where(
                            'gateway_result->credit->terminal_nsu',
                            '=',
                            $nsuTerminal
                        );
                    }
                )->get();


                if (count($sale) == 0) {
                    report(new Exception("Venda não encontrada nos emails de contestação com o nsu terminal = " . $nsuTerminal));
                    continue;
                }
                if (count($sale) > 1) {
                    report(new Exception("Mais de uma venda encontrada nos emails de contestação com o nsu terminal = " . $nsuTerminal));
                    continue;
                }
                if (
                    (empty($sale->first()->contetations ?? "")) &&
                    (empty(SaleContestation::where('data->nsu_terminal', $nsuTerminal)->first()))
                ) {
                    SaleContestation::create([
                        'sale_id' => $sale->first()->id ?? null,
                        'data' => json_encode($data),
                    ]);
                }
            }
        }

        return true;
    }

    public function updateOldContestations()
    {
        $emailMessages = $this->getMessagesWithQuery(50,
            'from:(monitoria_marketplace@getnet.com.br) Valor do Chargeback', true);

        $result = DB::transaction(function () use ($emailMessages) {
            try {
                foreach ($emailMessages as $emailMessage) {
                    if (!empty($emailMessage)) {
                        $arr = $this->getDataFromEmail($emailMessage['text'], "p");
                        $nsuTerminal = $this->getValueFromEmail($arr[4]);

                        $contestation = SaleContestation::where("data->nsu_terminal", '=', $nsuTerminal)
                            ->where("data->nsu_transacao", '=', $this->getValueFromEmail($arr[3]))
                            ->whereNull('file_date')
                            ->first();

                        if ($contestation) {
                            $contestation->file_date = $emailMessage['date'];
                            $contestation->request_date = $emailMessage['date'];
                            $contestation->expiration_date = Carbon::parse($emailMessage['date'])->addDays(12);
                            $contestation->transaction_date = $contestation->sale->end_date;
                            $contestation->nsu = $nsuTerminal;
                            $contestation->save();
                        }
                    }
                }
            } catch (Exception $e) {
                report($e->getMessage());
            }
        });


        return true;
    }

    public function getFilesFromEmail($limit = 200, $recursive = false)
    {
        $this->clearFolder();


        $get_day_latest_import = SaleContestation::select('file_date')->orderBy('file_date', 'desc')->first();
        $date_after = Carbon::parse($get_day_latest_import->file_date);
        $date_after = $date_after->addDay()->format('Y/m/d');
        $service = new \Google_Service_Gmail($this->client);
        $userId = 'me';
        $maxResults = $limit;
        $pageToken = null;
        $q = 'from:(monitoria_marketplace@getnet.com.br) [MGM GETNET] E-Request CLOUDFOX INTERMEDIACAO DE SERVICOS E NEGOCIOS after:' . $date_after;
        $labelIds = array('INBOX'); //CHAT - SENT - INBOX - IMPORTANT - TRASH - DRAFT - SPAM - CATEGORY_FORUMS - CATEGORY_UPDATES - CATEGORY_PERSONAL - CATEGORY_PROMOTIONS - CATEGORY_SOCIAL - STARRED - UNREAD
        $includeSpamTrash = false;
        $alt = 'json';

        $messages = array();

        $opt_params = [
            'maxResults' => $maxResults,
            'pageToken' => $pageToken,
            'q' => $q,
            'labelIds' => $labelIds,
            'includeSpamTrash' => $includeSpamTrash,
            'alt' => $alt
        ];

        $paths = [];

        if ($recursive == true) {
            do {
                try {
                    if ($pageToken) {
                        $opt_params['pageToken'] = $pageToken;
                    }

                    $messagesResponse = $service->users_messages->listUsersMessages($userId, $opt_params);

                    if ($messagesResponse->getMessages()) {
                        $messages = array_merge($messages, $messagesResponse->getMessages());
                        $pageToken = $messagesResponse->getNextPageToken();
                    }
                } catch (Exception $e) {
                    print 'An error occurred: ' . $e->getMessage();
                }
            } while ($pageToken);
        } else {
            $messages = $service->users_messages->listUsersMessages($userId, $opt_params);
        }

        foreach ($messages as $message) {
            $msg = $service->users_messages->get($userId, $message->getId());
            $parts = $msg->getPayload()->getParts();


            if (count($parts) > 0) {
                $attachmentId = $parts[1]->getBody()->getAttachmentId();
            } else {
                $attachmentId = $msg->getPayload()->getBody()->getAttachmentId();
            }

            $data = $service->users_messages_attachments->get('chargebackgetnet@gmail.com', $message->getId(),
                $attachmentId);

            $file_path = 'contestation/' . str_random(30) . time() . '.txt';
            $this->storageDrive->put($file_path, $this->base64url_decode(utf8_encode($data->data)), 'public');
            $paths[] = $this->storageDrive->url($file_path);
        }

        return $paths;
    }

}
