<?php

namespace Modules\UserInformations\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\UserInformation;
use Modules\UserInformations\Http\Requests\UserInformationsRequest;
use Symfony\Component\HttpFoundation\Response;

class UserInformationsApiController extends Controller
{
    public function store(UserInformationsRequest $request)
    {
        try {
            $data = $request->all();

            $model = new UserInformation();
            $exists = $model->where('document', $data['document'])->first();

            if (empty($exists)) {
                //Create

                if ($data['status'] == 1) {
                    return $this->sendSimpleJson(['status'=>'ok', 'msg'=>'']);
                }

                $model->document = $data['document'];
                $model->email = $data['email'];
                $model = $this->setData($model, $data);
                $model->save();

                return response()->json([
                    'message' => 'Informações do usuário cadastradas'
                ], Response::HTTP_OK);
            } else {
                // Update

                $exists->email = $data['email'];
                $model = $this->setData($exists, $data);
                $model->save();

                return response()->json([
                    'message' => 'Informações do usuário atualizada'
                ], Response::HTTP_OK);
            }
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function setData($model, $data)
    {
        if (! empty($data['status'])) {
            $model->status = $data['status'];
        }

        if (! empty($data['name'])) {
            $model->name = $data['name'];
        }
        if (! empty($data['phone'])) {
            $model->phone = $data['phone'];
        }
        if (! empty($data['last_step'])) {
            $model->last_step = $data['last_step'];
        }
        if (! empty($data['zip_code'])) {
            $model->zip_code = $data['zip_code'];
        }
        if (! empty($data['country'])) {
            $model->country = $data['country'];
        }
        if (! empty($data['state'])) {
            $model->state = $data['state'];
        }
        if (! empty($data['city'])) {
            $model->city = $data['city'];
        }
        if (! empty($data['district'])) {
            $model->district = $data['district'];
        }
        if (! empty($data['street'])) {
            $model->street = $data['street'];
        }
        if (! empty($data['number'])) {
            $model->number = $data['number'];
        }
        if (! empty($data['complement'])) {
            $model->complement = $data['complement'];
        }
        if (! empty($data['company_document'])) {
            $model->company_document = $data['company_document'];
        }
        if (! empty($data['company_zip_code'])) {
            $model->company_zip_code = $data['company_zip_code'];
        }
        if (! empty($data['company_state'])) {
            $model->company_state = $data['company_state'];
        }
        if (! empty($data['company_street'])) {
            $model->company_street = $data['company_street'];
        }
        if (! empty($data['company_city'])) {
            $model->company_city = $data['company_city'];
        }
        if (! empty($data['company_district'])) {
            $model->company_district = $data['company_district'];
        }
        if (! empty($data['company_number'])) {
            $model->company_number = $data['company_number'];
        }
        if (! empty($data['company_complement'])) {
            $model->company_complement = $data['company_complement'];
        }
        if (! empty($data['bank'])) {
            $model->bank = $data['bank'];
        }
        if (! empty($data['agency'])) {
            $model->agency = $data['agency'];
        }
        if (! empty($data['agency_digit'])) {
            $model->agency_digit = $data['agency_digit'];
        }
        if (! empty($data['account'])) {
            $model->account = $data['account'];
        }
        if (! empty($data['account_digit'])) {
            $model->account_digit = $data['account_digit'];
        }
        if (! empty($data['monthly_income'])) {
            $model->monthly_income = $data['monthly_income'];
        }
        if (! empty($data['niche'])) {
            $model->niche = $data['niche'];
        }
        if (! empty($data['website_url'])) {
            $model->website_url = $data['website_url'];
        }
        if (! empty($data['gateway'])) {
            $model->gateway = $data['gateway'];
        }
        if (! empty($data['store'])) {
            $model->ecommerce = $data['store'];
        }
        if (! empty($data['cloudfox_referer'])) {
            $model->cloudfox_referer = $data['cloudfox_referer'];
        }

        return $model;
    }
}
