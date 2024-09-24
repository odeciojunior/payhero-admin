<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modules\Core\Entities\SaleInformation
 *
 * @property int $id
 * @property int|null $sale_id
 * @property mixed|null $request
 * @property string|null $operational_system Sistema operacional do usuário
 * @property string|null $browser Browser usado para fazer a compra
 * @property string|null $browser_fingerprint Fingerprint do browser utilizado na compra
 * @property string|null $browser_token Token do browser (salvo no cookie) utilizado na compra
 * @property string|null $browser_token_post
 * @property string|null $ip Ip com comprador
 * @property string|null $url Url de origem da compra
 * @property string|null $referer Referer de origem da compra
 * @property string|null $return_url Url de retorno da compra
 * @property string|null $customer_name Nome do comprador
 * @property string|null $customer_email Email do comprador
 * @property string|null $customer_phone Telefone do comprador
 * @property string|null $customer_identification_number CPF/CNPJ do comprador
 * @property string|null $project_name Nome do projeto
 * @property string|null $transaction_amount Valor total da transação
 * @property string|null $country País informado pelo comprador
 * @property string|null $zip_code CEP informado pelo comprador
 * @property string|null $state Estado informado pelo comprador
 * @property string|null $city Cidade informada pelo comprador
 * @property string|null $district Bairro informado pelo comprador
 * @property string|null $street_name Rua informada pelo comprador
 * @property string|null $street_number Número da casa informada pelo comprador
 * @property string|null $card_token Token do cartão
 * @property string|null $card_brand Bandeira do cartão
 * @property int|null $installments Número de parcelas na compra
 * @property int|null $first_six_digits Primeiros 6 dígitos do cartão
 * @property int|null $last_four_digits Últimos 4 dígitos do cartão
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Sale|null $sale
 * @method static Builder|SaleInformation newModelQuery()
 * @method static Builder|SaleInformation newQuery()
 * @method static Builder|SaleInformation query()
 * @method static Builder|SaleInformation whereBrowser($value)
 * @method static Builder|SaleInformation whereBrowserFingerprint($value)
 * @method static Builder|SaleInformation whereBrowserToken($value)
 * @method static Builder|SaleInformation whereBrowserTokenPost($value)
 * @method static Builder|SaleInformation whereCardBrand($value)
 * @method static Builder|SaleInformation whereCardToken($value)
 * @method static Builder|SaleInformation whereCity($value)
 * @method static Builder|SaleInformation whereCountry($value)
 * @method static Builder|SaleInformation whereCreatedAt($value)
 * @method static Builder|SaleInformation whereCustomerEmail($value)
 * @method static Builder|SaleInformation whereCustomerIdentificationNumber($value)
 * @method static Builder|SaleInformation whereCustomerName($value)
 * @method static Builder|SaleInformation whereCustomerPhone($value)
 * @method static Builder|SaleInformation whereDistrict($value)
 * @method static Builder|SaleInformation whereFirstSixDigits($value)
 * @method static Builder|SaleInformation whereId($value)
 * @method static Builder|SaleInformation whereInstallments($value)
 * @method static Builder|SaleInformation whereIp($value)
 * @method static Builder|SaleInformation whereLastFourDigits($value)
 * @method static Builder|SaleInformation whereOperationalSystem($value)
 * @method static Builder|SaleInformation whereProjectName($value)
 * @method static Builder|SaleInformation whereRequest($value)
 * @method static Builder|SaleInformation whereSaleId($value)
 * @method static Builder|SaleInformation whereState($value)
 * @method static Builder|SaleInformation whereStreetName($value)
 * @method static Builder|SaleInformation whereStreetNumber($value)
 * @method static Builder|SaleInformation whereTransactionAmount($value)
 * @method static Builder|SaleInformation whereUpdatedAt($value)
 * @method static Builder|SaleInformation whereZipCode($value)
 */
class SaleInformation extends Model
{
    use HasFactory;

    protected $table = "sale_informations";

    protected $keyType = "integer";

    protected $fillable = [
        "sale_id",
        "operational_system",
        "browser",
        "browser_fingerprint",
        "browser_token",
        "ip",
        "url",
        "referer",
        "return_url",
        "customer_name",
        "customer_email",
        "customer_phone",
        "customer_identification_number",
        "project_name",
        "transaction_amount",
        "country",
        "zip_code",
        "state",
        "city",
        "district",
        "street_name",
        "street_number",
        "card_token",
        "card_brand",
        "installments",
        "first_six_digits",
        "last_four_digits",
        "created_at",
        "updated_at",
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
