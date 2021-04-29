<div class="modal fade rt-modal" id="upgradeAccountModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Please Upgrade Your Account</h4>
            </div>
            <div class="modal-body">
                <div class="search-box">
                    <p>
                        @if($company->trial_period==3)
                            In your current subscription, only the super admin can be active. To add more users, please upgrade your subscription to a higher plan.
                        @else
                            Your subscription allows a maximum of {{ $maxSubscriptionUser }} active users. Please upgrade your subscription to a higher plan to add more users.
                        @endif
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal" style="margin-left:15px;">CLOSE</button>
                <a href="{{ route('Company.Subscription.Upgrade') }}" class="btn btn-primary pull-right">Upgrade Subscription</a>
            </div>
        </div>
    </div>
</div>