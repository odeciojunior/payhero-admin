<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\User;

class UserService
{
    public function isDocumentValidated($userId = null): bool
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

    public function haveAnyDocumentPending(): bool
    {
        $userModel = new User();
        $user = auth()->user();
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

    public function haveAnyDocumentRefused(): bool
    {
        $userModel = new User();
        $user = auth()->user();
        $userPresenter = $userModel->present();

        if (!empty($user)) {
            if ($user->address_document_status == $userPresenter->getAddressDocumentStatus('refused') ||
                $user->personal_document_status == $userPresenter->getPersonalDocumentStatus('refused')) {
                return true;
            }
        }

        return false;
    }

    public function getRefusedDocuments()
    {
        $userModel = new User();
        $userPresenter = $userModel->present();
        $user = auth()->user();
        $refusedDocuments = collect();
        if (!empty($user)) {
            foreach ($user->userDocuments as $document) {
                if (!empty($document->refused_reason)) {
                    $dataDocument = [
                        'date' => $document->created_at->format('d/m/Y'),
                        'type_translated' => __(
                            'definitions.enum.user_document_type.' . $userPresenter->getDocumentType(
                                $document->document_type_enum
                            )
                        ),
                        'document_url' => $document->document_url,
                        'refused_reason' => $document->refused_reason,
                    ];
                    $refusedDocuments->push(collect($dataDocument));
                }
            }
        }

        return $refusedDocuments;
    }

    public function verifyCpf($cpf): bool
    {
        $userModel = new User();
        $cpf = preg_replace("/[^0-9]/", "", $cpf);
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

    public function verifyFieldsEmptyBraspag(User $user)
    {
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
        } else {
            return false;
        }
    }

    public function verifyFieldsEmpty(User $user): bool
    {
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
        } else {
            return false;
        }
    }

    public function unfilledFields(User $user): array
    {
        $arrayFields = [];

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
        if (empty($user->sex)) {
            $arrayFields[] = 'sex';
        }
        if (empty($user->mother_name)) {
            $arrayFields[] = 'mother_name';
        }
        return $arrayFields;
    }

    public function verifyIsValidCPF($cpf): bool
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

    public function verifyExistsCPF($cpf): bool
    {
        $userModel = new User();
        $cpf = preg_replace("/[^0-9]/", "", $cpf);

        $user = $userModel->where('document', $cpf)->first();
        if (!empty($user)) {
            return true;
        }

        return false;
    }

    public function getUserByIdwallCPF($cpf)
    {
        try {
            $idewallService = new IdwallService();
            $userStatus = json_decode($idewallService->getGenerateProtocolByCPF($cpf), true);
            $userProtocol = $userStatus['result']['numero'];

            /**
             * SLEEP É NECESSÁRIO PARA TER TEMPO DE PROCESSAR O RELATÓRIO
             */
            sleep(3);

            if (!empty($userProtocol) && $userStatus['status_code'] == 200) {
                $user = $idewallService->getReportByProtocolNumber($userProtocol);

                return json_decode($user, true);
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function userWithdrawalBlocked($user): bool
    {
        if ($user->status == (new User())->present()->getStatus('withdrawal blocked')) {
            return true;
        }

        return false;
    }
}
