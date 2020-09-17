<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Companies\Transformers\CompanyResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserInformation;

/**
 * Class CompaniesService
 * @package Modules\Core\Services
 */
class UserService
{
    public function isDocumentValidated($userId = null)
    {
        $userModel = new User();
        if (empty($userId)) {
            $user = auth()->user();
        } else {
            $user = User::find($userId);
        }

        $userPresenter = $userModel->present();
        if (!empty($user)) {
            if ($user->address_document_status == $userPresenter->getAddressDocumentStatus('approved') &&
                $user->personal_document_status == $userPresenter->getPersonalDocumentStatus('approved')) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    public function haveAnyDocumentPending()
    {
        $userModel     = new User();
        $user          = auth()->user();
        $userPresenter = $userModel->present();

        if (!empty($user)) {
            if (($user->address_document_status == $userPresenter->getAddressDocumentStatus('approved') ||
                    $user->address_document_status == $userPresenter->getAddressDocumentStatus('analyzing')) &&
                ($user->personal_document_status == $userPresenter->getPersonalDocumentStatus('approved') ||
                    $user->personal_document_status == $userPresenter->getPersonalDocumentStatus('analyzing'))) {
                return false;
            }
        }

        return true;
    }

    public function getRefusedDocuments()
    {
        $userModel        = new User();
        $userPresenter    = $userModel->present();
        $user             = auth()->user();
        $refusedDocuments = collect();
        if (!empty($user)) {
            foreach ($user->userDocuments as $document) {
                if (!empty($document->refused_reason)) {
                    $dataDocument = [
                        'date'            => $document->created_at->format('d/m/Y'),
                        'type_translated' => __(
                            'definitions.enum.user_document_type.' . $userPresenter->getDocumentType(
                                $document->document_type_enum
                            )
                        ),
                        'document_url'    => $document->document_url,
                        'refused_reason'  => $document->refused_reason,
                    ];
                    $refusedDocuments->push(collect($dataDocument));
                }
            }
        }

        return $refusedDocuments;
    }

    public function verifyCpf($cpf)
    {
        $userModel     = new User();
        $cpf           = preg_replace("/[^0-9]/", "", $cpf);
        $userPresenter = $userModel->present();

        $user = $userModel->where(
            [
                ['document', $cpf],
                ['address_document_status', $userPresenter->getAddressDocumentStatus('approved')],
                ['personal_document_status', $userPresenter->getPersonalDocumentStatus('approved')],
            ]
        )->first();
        if (!empty($user)) {
            return true;
        }

        return false;
    }

    public function createUserInformationDefault($userId)
    {
        try {
            UserInformation::create(
                [
                    'user_id'       => $userId,
                    'document_type' => 1,
                ]
            );
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param User $user
     * @return bool
     */
    public function verifyFieldsEmpty(User $user)
    {
        $userInformation = $user->userInformation;

        if (empty($user->email)) {
            return true;
        } elseif (empty($user->cellphone)) {
            return true;
        } elseif (empty($user->document)) {
            return true;
        } elseif (empty($user->zip_code)) {
            return true;
        } elseif (empty($user->country)) {
            return true;
        } elseif (empty($user->state)) {
            return true;
        } elseif (empty($user->city)) {
            return true;
        } elseif (empty($user->neighborhood)) {
            return true;
        } elseif (empty($user->street)) {
            return true;
        } elseif (empty($user->number)) {
            return true;
        } elseif (empty($user->date_birth)) {
            return true;
        } elseif (empty($userInformation->sex)) {
            return true;
        } elseif (empty($userInformation->marital_status)) {
            return true;
        } elseif (empty($userInformation->nationality)) {
            return true;
        } elseif ($userInformation->present()->getMaritalStatus('married') == $userInformation->marital_status
            && empty($userInformation->spouse_name)) {
            return true;
        } elseif (empty($userInformation->birth_city)) {
            return true;
        } elseif (empty($userInformation->birth_country)) {
            return true;
        } elseif (empty($userInformation->monthly_income)) {
            return true;
        } elseif (empty($userInformation->document_number)) {
            return true;
        } elseif (empty($userInformation->document_issue_date)) {
            return true;
        } elseif (empty($userInformation->document_issuer)) {
            return true;
        } elseif (empty($userInformation->document_issuer_state)) {
            return true;
        } else {
            return false;
        }
        /*
         * mother_name
         * father_name
         * document_serial_number
         * document_expiration_date
         */
    }

    /**
     * @param User $user
     * @return array
     */
    public function unfilledFields(User $user)
    {
        $arrayFields     = [];
        $userInformation = $user->userInformation;
        if (empty($user->document)) {
            $arrayFields[] = 'document';
        }
        if (empty($user->zip_code)) {
            $arrayFields[] = 'zip_code';
        }
        if (empty($user->country)) {
            $arrayFields[] = 'country';
        }
        if (empty($user->state)) {
            $arrayFields[] = 'state';
        }
        if (empty($user->city)) {
            $arrayFields[] = 'city';
        }
        if (empty($user->neighborhood)) {
            $arrayFields[] = 'neighborhood';
        }
        if (empty($user->street)) {
            $arrayFields[] = 'street';
        }
        if (empty($user->number)) {
            $arrayFields[] = 'number';
        }
        if (empty($user->date_birth)) {
            $arrayFields[] = 'date_birth';
        }
        if (empty($userInformation->sex)) {
            $arrayFields[] = 'sex';
        }
        if (empty($userInformation->mother_name)) {
            $arrayFields[] = 'mother_name';
        }
        if (empty($userInformation->marital_status)) {
            $arrayFields[] = 'marital_status';
        }
        if (empty($userInformation->nationality)) {
            $arrayFields[] = 'nationality';
        }
        if (!empty($userInformation) && $userInformation->present()->getMaritalStatus('married') == $userInformation->marital_status
            && empty($userInformation->spouse_name)) {
            $arrayFields[] = 'spouse_name';
        }
        if (empty($userInformation->birth_city)) {
            $arrayFields[] = 'birth_city';
        }
        if (empty($userInformation->birth_country)) {
            $arrayFields[] = 'birth_country';
        }
        if (empty($userInformation->monthly_income)) {
            $arrayFields[] = 'monthly_income';
        }
        if (empty($userInformation->document_number)) {
            $arrayFields[] = 'document_number';
        }
        if (empty($userInformation->document_issue_date)) {
            $arrayFields[] = 'document_issue_date';
        }
        if (empty($userInformation->document_issuer)) {
            $arrayFields[] = 'document_issuer';
        }
        if (empty($userInformation->document_issuer_state)) {
            $arrayFields[] = 'document_issuer_state';
        }
        if (empty($userInformation->document_expiration_date)) {
            $arrayFields[] = 'document_expiration_date';
        }
        return $arrayFields;
    }


    public function verifyIsValidCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);

        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }
}
