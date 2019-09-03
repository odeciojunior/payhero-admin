<?php

namespace Modules\Products\Policies;

use Modules\Core\Entities\User;
use Modules\Core\Entities\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param User $user
     * @param Product $product
     * @return bool
     */
    public function edit(User $user, Product $product)
    {
        if ($user->id == $product->user) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @param Product $product
     * @return bool
     */
    public function update(User $user, Product $product)
    {
        if ($user->id == $product->user) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @param Product $product
     * @return bool
     */
    public function destroy(User $user, Product $product)
    {
        if ($user->id == $product->user) {
            return true;
        } else {
            return false;
        }
    }
}
