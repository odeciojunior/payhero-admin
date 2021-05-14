<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class DeliveryPresenter extends Presenter
{
    /**
     * @return string
     */
    public function getCep()
    {
        return substr($this->zip_code, 0, 5) . '-' . substr($this->zip_code, 5, 3);
    }

    /**
     * @return string
     */
    public function getReceiverFirstName()
    {
        return explode(' ', $this->receiver_name)[0];
    }

    /**
     * @return string
     */
    public function getReceiverLastName()
    {
        return explode(' ', $this->receiver_name)[count(explode(' ', $this->receiver_name)) - 1];
    }

}

