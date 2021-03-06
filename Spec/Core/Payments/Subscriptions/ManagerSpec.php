<?php

namespace Spec\Minds\Core\Payments\Subscriptions;

use Minds\Core\Di\Di;
use Minds\Core\Payments\Manager;
use Minds\Core\Payments\Subscriptions\Repository;
use Minds\Core\Payments\Subscriptions\Subscription;
use Minds\Entities\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ManagerSpec extends ObjectBehavior
{
    protected $repository;

    function let(
        Repository $repository
    )
    {
        $this->repository = $repository;

        $this->beConstructedWith($repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Minds\Core\Payments\Subscriptions\Manager');
    }

    function it_should_create()
    {
        $user = new User;
        $user->guid = 123;

        $subscription = new Subscription;
        $subscription->setId('sub_test')
            ->setPlanId('spec')
            ->setPaymentMethod('spec')
            ->setUser($user);

        $this->repository->add($subscription)
            ->shouldBeCalled()
            ->willReturn(true);

        $this->setSubscription($subscription);
         $this->create()
            ->shouldReturn(true);
    }

    function it_should_calculate_the_next_billing_during_create()
    {
        $user = new User;
        $user->guid = 123;

        $subscription = new Subscription;
        $subscription->setId('sub_test')
            ->setPlanId('spec')
            ->setPaymentMethod('spec')
            ->setInterval('daily')
            ->setLastBilling(time())
            ->setUser($user);

        $this->repository->add($subscription)
            ->shouldBeCalled()
            ->willReturn(true);

        $this->setSubscription($subscription);
        $this->create()
            ->shouldReturn(true);
    }

    function it_should_throw_if_not_valid()
    {
        $subscription = new Subscription;
        $subscription->setId('sub_test');

        $this->setSubscription($subscription);
        $this->shouldThrow('\Exception')->duringCreate();
    }

    function it_should_update()
    {
        $user = new User;
        $user->guid = 123;

        $subscription = new Subscription;
        $subscription->setId('sub_test')
            ->setPlanId('spec')
            ->setPaymentMethod('spec')
            ->setUser($user);

        $this->repository->update($subscription)
            ->shouldBeCalled()
            ->willReturn(true);

        $this->setSubscription($subscription);
        $this->update()
            ->shouldReturn(true);
    }

    function it_should_cancel()
    {
        $user = new User;
        $user->guid = 123;

        $subscription = new Subscription;
        $subscription->setId('sub_test')
            ->setPlanId('spec')
            ->setPaymentMethod('spec')
            ->setInterval('daily')
            ->setUser($user);

        $this->repository->delete($subscription)
            ->shouldBeCalled()
            ->willReturn(true);

        $this->setSubscription($subscription);
        $this->cancel()->shouldReturn(true);
    }

    function it_should_throw_if_no_type_during_cancel()
    {
        $subscription = new Subscription;
        $subscription->setId('sub_test');

        $this->setSubscription($subscription);
        $this->shouldThrow('\Exception')->duringCancel();
    }

    function it_should_get_next_billing_for_daily_recurring()
    {
        $subscription = new Subscription;
        $subscription->setInterval('daily');
        $subscription->setLastBilling(strtotime('2000-01-01T12:00:00+00:00'));
        $next = strtotime('+1 day', $subscription->getLastBilling());

        $this->setSubscription($subscription);
        $this->getNextBilling()
            ->shouldReturn($next);
    }

    function it_should_get_next_billing_for_monthly_recurring()
    {
        $subscription = new Subscription;
        $subscription->setInterval('monthly');
        $subscription->setLastBilling(strtotime('2000-01-01T12:00:00+00:00'));
        $next = strtotime('+1 month', $subscription->getLastBilling());

        $this->setSubscription($subscription);
        $this->getNextBilling()
            ->shouldReturn($next);
    }

    function it_should_get_next_billing_for_yearly_recurring()
    {
        $subscription = new Subscription;
        $subscription->setInterval('yearly');
        $subscription->setLastBilling(strtotime('2000-01-01T12:00:00+00:00'));
        $next = strtotime('+1 year', $subscription->getLastBilling());

        $this->setSubscription($subscription);
        $this->getNextBilling()
            ->shouldReturn($next);
    }

    /*function it_should_get_next_billing_as_null_for_custom_recurring()
    {
        $last_billing = 10000000;

        $this
            ->getNextBilling($last_billing, 'custom')
            ->shouldReturn(null);
    }

    function it_should_get_next_billing_as_null_for_empty_last_billing()
    {
        $this
            ->getNextBilling(null, 'custom')
            ->shouldReturn(null);
    }

    function it_should_get_next_billing_converting_date_time_to_timestamp()
    {
        $last_billing = strtotime('2000-01-01T12:00:00+00:00');
        $next_billing = strtotime('+1 day', $last_billing);

        $this
            ->getNextBilling(new \DateTime("@{$last_billing}"), 'daily')
            ->shouldReturn($next_billing);
    }

    function it_should_throw_if_invalid_recurring_during_get_next_billing()
    {
        $last_billing = 10000000;

        $this
            ->shouldThrow(new \Exception('Invalid recurring value'))
            ->duringGetNextBilling($last_billing, '^}invalid-recurring-value');
    }*/

}
