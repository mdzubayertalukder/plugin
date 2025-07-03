<?php

namespace Plugin\Saas\Http\Rules;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Rule;
use Plugin\Saas\Repositories\SubscriptionRepository;

class AlreadySubscribedPlan implements Rule
{
    public $request;
    public function __construct($request)
    {
        $this->request = $request;
    }
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $sub_repo = new SubscriptionRepository();
        $is_already_subscribed_with_same_plan = $sub_repo->isUserAlreadySubscribed($this->request);
        return !$is_already_subscribed_with_same_plan;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You are already subscribed to this plan.';
    }
}
