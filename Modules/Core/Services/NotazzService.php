<?php

namespace Modules\Core\Services;

use App\Entities\NotazzInvoice;
use App\Entities\Sale;
use Carbon\Carbon;
use Vinkla\Hashids\Facades\Hashids;

class NotazzService
{
    /**
     * @var
     */
    private $notazzToken;
    /**
     * @var
     */
    private $notazzApp;

    /**
     * NotazzService constructor.
     */
    public function __construct()
    {
        //$this->notazzToken = $notazzToken;
    }

    /**
     * @param $fields
     * @return mixed
     */
    function sendRequest($fields)
    {
        $fields        = ["fields" => $fields];
        $fields_string = '';

        //url-ify the data for the POST
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }

        rtrim($fields_string, '&');

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, 'https://app.notazz.com/api');
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        //execute post
        $response = curl_exec($ch);

        //close connection
        curl_close($ch);

        //Convertendo json para array
        $pos = strpos($response, '{');

        return (json_decode(substr($response, $pos), true));
    }

    /**
     * @param $data
     */
    public function sendNfse($data)
    {

        $this->sendRequest($data);
    }

    /**
     * @param $data
     */
    public function updateNfse($data)
    {

        $this->sendRequest($data);
    }

    /**
     *
     */
    public function verifyPendingInvoices()
    {
        $notazzInvoicesModel = new NotazzInvoice();

        $notazzInvoices = $notazzInvoicesModel->with([
                                                         'sale',
                                                     ])
                                              ->where('status', $notazzInvoicesModel->present()->getStatus('pending'))
                                              ->get();

        foreach ($notazzInvoices as $notazzInvoice) {
            $data = [
                '',

            ];

            $this->sendNfse();
        }
    }

    /**
     * @param $notazzIntegrationId
     * @param $saleId
     * @param int $invoiceType
     * @param null $invoiceSchedule
     * @return bool
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function createInvoice($notazzIntegrationId, $saleId, $invoiceType = 1, $invoiceSchedule = null)
    {
        if (!empty($saleId) && !empty($notazzIntegrationId)) {
            if (empty($invoiceSchedule)) {
                //executar 1h depois
                $schedule = Carbon::now()->addHour()->toDateTime();
            } else {
                $schedule = $invoiceSchedule;
            }

            $notazzInvoiceModel = new NotazzInvoice();

            $notazzInvoice = $notazzInvoiceModel->create([
                                                             'sale_id'               => $saleId,
                                                             'notazz_integration_id' => $notazzIntegrationId,
                                                             'invoice_type'          => $invoiceType,
                                                             'notazz_id'             => null,
                                                             'external_id'           => Hashids::encode($saleId),
                                                             'status'                => $notazzInvoiceModel->present()
                                                                                                           ->getStatus('pending'),
                                                             'canceled_flag'         => false,
                                                             'schedule'              => $schedule,
                                                         ]);

            if ($notazzInvoice) {
                return true;
            } else {
                return false;
            }
        } else {
            //nenhum venda selecionada
            return false;
        }
    }
}
