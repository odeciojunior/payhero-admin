<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class ClientPresenter extends Presenter
{
    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return explode(' ', $this->name)[0];
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return explode(' ', $this->name)[count(explode(' ', $this->name)) - 1];
    }

    /**
     * @return string
     */
    public function getTelephone($ddd = false, $number = false)
    {
        $this->telephone = preg_replace("/\D/", '', $this->telephone);

        if(!$ddd && !$number){

            $length = strlen(preg_replace("/[^0-9]/", "", $this->telephone));
            if ($length == 13) { // COM CÓDIGO DE ÁREA NACIONAL E DO PAIS e 9 dígitos
                return "+" . substr($this->telephone, 0, $length - 11) . "(" . substr($this->telephone, $length - 11, 2) . ")" . substr($this->telephone, $length - 9, 5) . "-" . substr($this->telephone, -4);
            }
            if ($length == 12) { // COM CÓDIGO DE ÁREA NACIONAL E DO PAIS
                return "+" . substr($this->telephone, 0, $length - 10) . "(" . substr($this->telephone, $length - 10, 2) . ")" . substr($this->telephone, $length - 8, 4) . "-" . substr($this->telephone, -4);
            }
            if ($length == 11) { // COM CÓDIGO DE ÁREA NACIONAL e 9 dígitos
                return "(" . substr($this->telephone, 0, 2) . ")" . substr($this->telephone, 2, 5) . "-" . substr($this->telephone, 7, 11);
            }
            if ($length == 10) { // COM CÓDIGO DE ÁREA NACIONAL
                return "(" . substr($this->telephone, 0, 2) . ")" . substr($this->telephone, 2, 4) . "-" . substr($this->telephone, 6, 10);
            }
            if ($length <= 9) { // SEM CÓDIGO DE ÁREA
                return substr($this->telephone, 0, $length - 4) . "-" . substr($this->telephone, -4);
            }
        }
        elseif($ddd){
            return substr($this->telephone, 0, 2);
        }
        else{
            $length = strlen(preg_replace("/[^0-9]/", "", $this->telephone));

            if ($length == 11) {
                return substr($this->telephone, 2, 5) . "-" . substr($this->telephone, 7, 11);
            }
            if ($length == 10) {
                return substr($this->telephone, 2, 4) . "-" . substr($this->telephone, 6, 10);
            }
            return '';
        }
    }

    /**
     * @return string|string[]|null
     */
    public function getDocument()
    {
        $this->document = preg_replace("/\D/", '', $this->document);

        if (strlen($this->document) === 11) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $this->document);
        }

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $this->document);
    }


}

