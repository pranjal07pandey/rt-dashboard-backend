<?php

namespace App\Helpers\V2;

class MessageDisplay
{
    const ERROR = 'An error has occurred please try again later.';
    const Success = 'Success';
    const InvalidAction = 'Invalid action ! Please try with valid action.';
    const InvalidRequest = 'Invalid Request';
    const InvalidData = 'Invalid Data';
    const InvoiceNotFound = 'Invoice not found';
    const LinkExpired = 'Your link has expired.';
    const UnAuthorized = 'Not authorized.';

    const EmailActivation = 'Please check your email to activate your account.';
    const InvalidEmail = 'Invalid Email address.';
    const EmailClientAdded = "Email client add successfully.";

    const PasswordChanged = "Password Changed Successfully.";
    const InvalidPassword = "Invalid  password.";
    const ProfileUpdateError = 'Profile Picture Update Fails.';
    const ProfileUpdateSuccess= 'Profile Picture Update Successfully.';

    const DocketAdded = 'Docket added successfully.';
    const DocketUpdated = 'Docket edited sucessfully.';
    const DocketNotFound = 'Docket not found.';
    const DocketAlreadyApproved = 'Docket already approved.';
    const DocketApproved = 'Docket approved successfully.';
    const DocketRejected = 'Docket rejected successfully.';
    const DocketExist = 'Docket already rejected.';

    const DocketDraftSave = 'Docket draft saved successfully.';
    const DocketDraftDelete = 'Docket draft deleted successfully.';

    const SubscriptionUpgrade = 'Please upgrade your subscription. Your active subscription has an ability to send a maximum of 1 invoice per month.';

    const ActiveTimerExist = 'There is already one active timer. Please check first and try again.';

    const InvalidPasswordAndUsername = 'Invalid username and password.';
    const ClientAddFail = "Email client add fails.";

    const NumberSystemCheck = 'Show Number System must me checked';

    const NameUpdated = "Name updated successfully.";

    const DeviceTokenUpdated = 'Device Token update sucessfully.';
    const DocketApproval = 'Docket approved.';

    const EmployeeLeaveAdded = 'Employee leave added successfully.';
    const EmployeeLeaveUpdate = 'Employee leave updated successfully.';

    const MachineAdded = 'Machine added successfully.';
    const MachineUpdate = 'Machine updated successfully.';
    const MachineDelete = 'Machine deleted successfully.';

    const Logout = "Logout.";
}
