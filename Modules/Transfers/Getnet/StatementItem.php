<?php


namespace Modules\Transfers\Getnet;


class StatementItem
{

    /*
    Tipo de registro | 1 - Resumo da transação.
    Tipo de registro | 2 - Detalhe da transação.
    Tipo de registro | 3 - Comissão.
    Tipo de registro | 4 - Ajustes.
    Tipo de registro | 5 - Chargeback.
     * */
    const TYPE_TRANSACTION = 'TRANSACTION';
    const TYPE_COMMISSION = 'COMMISSION';
    const TYPE_ADJUSTMENT = 'ADJUSTMENT';
    const TYPE_CHARGEBACK = 'CHARGEBACK';
    const TYPE_REVERSED = 'REVERSED';

    const PAID_WITH_CREDIT_CARD = 'CREDIT_CARD';
    const PAID_WITH_BANK_SLIP = 'BANK_SLIP';

    public ?Order $order;
    public Details $details;
    public ?string $paidWith;
    public string $type, $transactionDate, $expectedDate, $realizedDate;
    public float $amount;
    public bool $isInvite = false;

    public function __construct()
    {
        $this->order = null;
        $this->paidWith = null;
    }
}
