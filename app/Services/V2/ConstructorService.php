<?php

namespace App\Services\V2;

use App\Repositories\V2\AssignedDocketRepository;
use App\Repositories\V2\CompanyRepository;
use App\Repositories\V2\DocketFieldFooterRepository;
use App\Repositories\V2\DocketFieldNumberRepository;
use App\Repositories\V2\DocketFieldRepository;
use App\Repositories\V2\DocketFiledPreFillerRepository;
use App\Repositories\V2\DocketGridAutoPrefillerRepository;
use App\Repositories\V2\DocketGridPrefillerRepository;
use App\Repositories\V2\DocketManualTimerBreakRepository;
use App\Repositories\V2\DocketManualTimerRepository;
use App\Repositories\V2\DocketPrefillerRepository;
use App\Repositories\V2\DocketPrefillerValueRepository;
use App\Repositories\V2\DocketRepository;
use App\Repositories\V2\DocketTallyableUnitRateRepository;
use App\Repositories\V2\DocketUnitRateRepository;
use App\Repositories\V2\FolderRepository;
use App\Repositories\V2\SentDocketsRepository;
use App\Repositories\V2\TemplateAssignFolderRepository;
use App\Repositories\V2\EmployeeRepository;
use App\Repositories\V2\SentDocEditedValueRepository;
use App\Repositories\V2\SentDocketsValueRepository;
use App\Repositories\V2\SubscriptionLogRepository;
use App\Repositories\V2\EmailUserRepository;
use App\Repositories\V2\SentDocketTimerAttachmentRepository;
use App\Repositories\V2\TimerRepository;
use App\Repositories\V2\EmailSentDocketRecipientRepository;
use App\Repositories\V2\EmailSentDocketValueRepository;
use App\Repositories\V2\EmailSentDocketImageValueRepository;
use App\Repositories\V2\DocketFieldGridValueRepository;
use App\Repositories\V2\DocketFieldGridLabelRepository;
use App\Repositories\V2\YesNoDocketsFieldRepository;
use App\Repositories\V2\SentEmailAttachmentRepository;
use App\Repositories\V2\SentEmailDocketInvoiceRepository;
use App\Repositories\V2\EmailSentDocketTallyUnitRateValRepository;
use App\Repositories\V2\EmailSentDocManualTimerBrkRepository;
use App\Repositories\V2\EmailSentDocManualTimerRepository;
use App\Repositories\V2\DocketProjectRepository;
use App\Repositories\V2\SentDocketProjectRepository;
use App\Repositories\V2\FolderItemRepository;
use App\Repositories\V2\SentEmailDocValYesNoValueRepository;
use App\Repositories\V2\SentDocValYesNoValueRepository;
use App\Repositories\V2\EmailSentDocketRepository;
use App\Repositories\V2\EmailSentDocketUnitRateValueRepository;
use App\Repositories\V2\SentDocketRecipientApprovalRepository;
use App\Repositories\V2\SentDocketInvoiceRepository;
use App\Repositories\V2\AssignedInvoiceRepository;
use App\Repositories\V2\ClientRepository;
use App\Repositories\V2\CompanyXeroRepository;
use App\Repositories\V2\EmailClientRepository;
use App\Repositories\V2\EmailSentInvoiceDescriptionRepository;
use App\Repositories\V2\EmailSentInvoiceImageRepository;
use App\Repositories\V2\EmailSentInvoicePaymentDetailRepository;
use App\Repositories\V2\EmailSentInvoiceRepository;
use App\Repositories\V2\EmailSentInvoiceValueRepository;
use App\Repositories\V2\InvoiceFieldRepository;
use App\Repositories\V2\InvoiceRepository;
use App\Repositories\V2\InvoiceXeroSettingRepository;
use App\Repositories\V2\SentEInvoiceAttachedEDocketRepository;
use App\Repositories\V2\SentInvoiceAttachedDocketRepository;
use App\Repositories\V2\SentInvoiceImageValueRepository;
use App\Repositories\V2\SentInvoiceXeroRepository;
use App\Repositories\V2\SentXeroInvoiceSettingRepository;
use App\Repositories\V2\SynXeroContactRepository;
use App\Repositories\V2\UserNotificationRepository;
use App\Repositories\V2\DocumentThemeRepository;
use App\Repositories\V2\MessagesGroupRepository;
use App\Repositories\V2\MessagesGroupUserRepository;
use App\Repositories\V2\MessagesRecipientsRepository;
use App\Repositories\V2\MessagesRepository;
use App\Repositories\V2\TimerAttachedTagRepository;
use App\Repositories\V2\TimerClientRepository;
use App\Repositories\V2\TimerCommentRepository;
use App\Repositories\V2\TimerImageRepository;
use App\Repositories\V2\TimerLogRepository;
use App\Repositories\V2\TimerSettingRepository;
use App\Repositories\V2\AppInfoRepository;
use App\Repositories\V2\AppleSubscriptionRepository;
use App\Repositories\V2\SentDocketRecipientRepository;
use App\Repositories\V2\UserRepository;
use App\Repositories\V2\DocketDraftRepository;
use App\Repositories\V2\SentInvoiceRepository;
use App\Repositories\V2\SentInvoiceDescriptionRepository;
use App\Repositories\V2\SentInvoiceValueRepository;
use App\Repositories\V2\SentInvoicePaymentDetailRepository;
use App\Repositories\V2\SentDocketRejectRepository;
use App\Repositories\V2\YesNoFieldRepository;
use App\Repositories\V2\DocketFieldGridRepository;
use App\Repositories\V2\LeaveRepository;
use App\Repositories\V2\InvoiceSettingRepository;
use App\Repositories\V2\MachineRepository;
use App\Repositories\V2\AssignDocketUserRepository;
use App\Repositories\V2\DocketDraftsAssignRepository;
use App\Repositories\V2\AssignDocketUserConnectionRepository;
use App\ThirdParyApis\FirebaseApi;


class ConstructorService{


    protected $docketRepository,$sentDocketsRepository,$assignedDocketRepository,$docketFieldRepository,
        $docketUnitRateRepository,$docketFieldFooterRepository,$docketManualTimerRepository,
        $docketManualTimerBreakRepository,$docketPrefillerValueRepository,$docketFieldNumberRepository,
        $docketPrefillerRepository,$docketFiledPreFillerRepository,$docketTallyableUnitRateRepository,
        $docketGridPrefillerRepository,$templateAssignFolderRepository,$folderRepository,
        $companyRepository,$docketGridAutoPrefillerRepository,$employeeRepository,$emailSentDocketRepository,
        $sentDocketsValueRepository,$sentDocEditedValueRepository,$subscriptionLogRepository,
        $emailUserRepository,$sentDocketTimerAttachmentRepository,$timerRepository,$emailSentDocketImageValueRepository,
        $emailSentDocketRecipientRepository,$emailSentDocketValueRepository,$docketFieldGridValueRepository,
        $docketFieldGridLabelRepository,$yesNoDocketsFieldRepository,$sentEmailAttachmentRepository,
        $sentEmailDocketInvoiceRepository,$emailSentDocketUnitRateValueRepository,$emailSentDocketTallyUnitRateValRepository,
        $emailSentDocManualTimerBrkRepository,$emailSentDocManualTimerRepository,$docketProjectRepository,
        $sentDocketProjectRepository,$folderItemRepository,$sentEmailDocValYesNoValueRepository,$sentDocValYesNoValueRepository,
        $sentDocketRecipientApprovalRepository,$sentDocketInvoiceRepository,$userNotificationRepository,$documentThemeRepository,
        $assignedInvoiceRepository,$invoiceRepository,$invoiceFieldRepository,$emailSentInvoiceDescriptionRepository,
        $emailSentInvoiceRepository,$emailClientRepository, $emailSentInvoicePaymentDetailRepository,$timerCommentRepository,
        $sentEInvoiceAttachedEDocketRepository,$emailSentInvoiceValueRepository,$emailSentInvoiceImageRepository,
        $sentInvoiceAttachedDocketRepository,$sentInvoiceImageValueRepository,$companyXeroRepository,$sentInvoiceXeroRepository,
        $sentXeroInvoiceSettingRepository,$invoiceXeroSettingRepository,$synXeroContactRepository,$timerClientRepository,
        $firebaseApi,$messagesGroupUserRepository,$messagesRecipientsRepository,$userRepository,$timerImageRepository,
        $messagesRepository,$messagesGroupRepository,$timerSettingRepository,$timerLogRepository,$sentInvoiceRepository,
        $timerAttachedTagRepository,$clientRepository,$appInfoRepository,$sentDocketRecipientRepository,$docketDraftRepository,
        $sentInvoiceDescriptionRepository,$sentInvoiceValueRepository,$sentInvoicePaymentDetailRepository,$sentDocketRejectRepository,
        $appleSubscriptionRepository,$yesNoFieldRepository,$docketFieldGridRepository,$leaveRepository,$invoiceSettingRepository,
        $machineRepository,$assignDocketUserRepository,$docketDraftsAssignRepository,$assignDocketUserConnectionRepository;

    public function __construct(DocketRepository $docketRepository,
        SentDocketsRepository $sentDocketsRepository,AssignedDocketRepository $assignedDocketRepository,
        DocketFieldRepository $docketFieldRepository, DocketUnitRateRepository $docketUnitRateRepository,
        DocketFieldFooterRepository $docketFieldFooterRepository,DocketManualTimerRepository $docketManualTimerRepository,
        DocketManualTimerBreakRepository $docketManualTimerBreakRepository, DocketPrefillerValueRepository $docketPrefillerValueRepository,
        DocketFieldNumberRepository $docketFieldNumberRepository, DocketPrefillerRepository $docketPrefillerRepository,
        DocketFiledPreFillerRepository $docketFiledPreFillerRepository, DocketTallyableUnitRateRepository $docketTallyableUnitRateRepository,
        DocketGridPrefillerRepository $docketGridPrefillerRepository, TemplateAssignFolderRepository $templateAssignFolderRepository,
        FolderRepository $folderRepository, CompanyRepository $companyRepository,SentDocketsValueRepository $sentDocketsValueRepository,
        DocketGridAutoPrefillerRepository $docketGridAutoPrefillerRepository, EmployeeRepository $employeeRepository,
        SentDocEditedValueRepository $sentDocEditedValueRepository, SubscriptionLogRepository $subscriptionLogRepository,
        EmailSentDocketRepository $emailSentDocketRepository,EmailUserRepository $emailUserRepository,
        SentDocketTimerAttachmentRepository $sentDocketTimerAttachmentRepository,TimerRepository $timerRepository,
        EmailSentDocketRecipientRepository $emailSentDocketRecipientRepository,EmailSentDocketValueRepository $emailSentDocketValueRepository,
        EmailSentDocketImageValueRepository $emailSentDocketImageValueRepository,DocketFieldGridValueRepository $docketFieldGridValueRepository,
        DocketFieldGridLabelRepository $docketFieldGridLabelRepository,YesNoDocketsFieldRepository $yesNoDocketsFieldRepository,
        SentEmailAttachmentRepository $sentEmailAttachmentRepository,SentEmailDocketInvoiceRepository $sentEmailDocketInvoiceRepository,
        EmailSentDocketUnitRateValueRepository $emailSentDocketUnitRateValueRepository,EmailSentDocManualTimerRepository $emailSentDocManualTimerRepository,
        EmailSentDocketTallyUnitRateValRepository $emailSentDocketTallyUnitRateValRepository,DocketProjectRepository $docketProjectRepository,
        EmailSentDocManualTimerBrkRepository $emailSentDocManualTimerBrkRepository,SentDocketProjectRepository $sentDocketProjectRepository,
        FolderItemRepository $folderItemRepository,SentEmailDocValYesNoValueRepository $sentEmailDocValYesNoValueRepository,
        SentDocValYesNoValueRepository $sentDocValYesNoValueRepository,SentDocketRecipientApprovalRepository $sentDocketRecipientApprovalRepository,
        SentDocketInvoiceRepository $sentDocketInvoiceRepository,UserNotificationRepository $userNotificationRepository,
        DocumentThemeRepository $documentThemeRepository,AssignedInvoiceRepository $assignedInvoiceRepository,InvoiceRepository $invoiceRepository,
        InvoiceFieldRepository $invoiceFieldRepository,EmailSentInvoiceDescriptionRepository $emailSentInvoiceDescriptionRepository,
        EmailSentInvoiceRepository $emailSentInvoiceRepository,EmailClientRepository $emailClientRepository,
                               FirebaseApi $firebaseApi,
        EmailSentInvoicePaymentDetailRepository $emailSentInvoicePaymentDetailRepository,SentEInvoiceAttachedEDocketRepository $sentEInvoiceAttachedEDocketRepository,
        EmailSentInvoiceValueRepository $emailSentInvoiceValueRepository,EmailSentInvoiceImageRepository $emailSentInvoiceImageRepository,
        SentInvoiceAttachedDocketRepository $sentInvoiceAttachedDocketRepository,SentInvoiceImageValueRepository $sentInvoiceImageValueRepository,
        CompanyXeroRepository $companyXeroRepository,SentInvoiceXeroRepository $sentInvoiceXeroRepository,SynXeroContactRepository $synXeroContactRepository,
        SentXeroInvoiceSettingRepository $sentXeroInvoiceSettingRepository,InvoiceXeroSettingRepository $invoiceXeroSettingRepository,
        MessagesGroupUserRepository $messagesGroupUserRepository,MessagesRecipientsRepository $messagesRecipientsRepository,ClientRepository $clientRepository,
        MessagesRepository $messagesRepository,MessagesGroupRepository $messagesGroupRepository,TimerSettingRepository $timerSettingRepository,
        TimerClientRepository $timerClientRepository,TimerImageRepository $timerImageRepository, TimerCommentRepository $timerCommentRepository,
        TimerAttachedTagRepository $timerAttachedTagRepository, AppInfoRepository $appInfoRepository,TimerLogRepository $timerLogRepository,
        SentDocketRecipientRepository $sentDocketRecipientRepository,UserRepository $userRepository,DocketDraftRepository $docketDraftRepository,
        SentInvoiceRepository $sentInvoiceRepository, SentInvoiceDescriptionRepository $sentInvoiceDescriptionRepository,
        SentInvoiceValueRepository $sentInvoiceValueRepository,SentInvoicePaymentDetailRepository $sentInvoicePaymentDetailRepository,
        SentDocketRejectRepository $sentDocketRejectRepository, AppleSubscriptionRepository $appleSubscriptionRepository,
        YesNoFieldRepository $yesNoFieldRepository,DocketFieldGridRepository $docketFieldGridRepository,LeaveRepository $leaveRepository,
        InvoiceSettingRepository $invoiceSettingRepository,MachineRepository $machineRepository,AssignDocketUserRepository $assignDocketUserRepository,
        DocketDraftsAssignRepository $docketDraftsAssignRepository,AssignDocketUserConnectionRepository $assignDocketUserConnectionRepository)
    {


        $this->docketRepository = $docketRepository;
        $this->sentDocketsRepository = $sentDocketsRepository;
        $this->assignedDocketRepository = $assignedDocketRepository;
        $this->docketFieldRepository = $docketFieldRepository;
        $this->docketUnitRateRepository = $docketUnitRateRepository;
        $this->docketFieldFooterRepository = $docketFieldFooterRepository;
        $this->docketManualTimerRepository = $docketManualTimerRepository;
        $this->docketManualTimerBreakRepository = $docketManualTimerBreakRepository;
        $this->docketPrefillerValueRepository = $docketPrefillerValueRepository;
        $this->docketFieldNumberRepository = $docketFieldNumberRepository;
        $this->docketPrefillerRepository = $docketPrefillerRepository;
        $this->docketFiledPreFillerRepository = $docketFiledPreFillerRepository;
        $this->docketTallyableUnitRateRepository = $docketTallyableUnitRateRepository;
        $this->docketGridPrefillerRepository = $docketGridPrefillerRepository;
        $this->templateAssignFolderRepository = $templateAssignFolderRepository;
        $this->folderRepository = $folderRepository;
        $this->companyRepository = $companyRepository;
        $this->docketGridAutoPrefillerRepository = $docketGridAutoPrefillerRepository;
        $this->employeeRepository = $employeeRepository;
        $this->sentDocketsValueRepository = $sentDocketsValueRepository;
        $this->sentDocEditedValueRepository = $sentDocEditedValueRepository;
        $this->subscriptionLogRepository = $subscriptionLogRepository;
        $this->emailSentDocketRepository = $emailSentDocketRepository;
        $this->emailUserRepository = $emailUserRepository;
        $this->sentDocketTimerAttachmentRepository = $sentDocketTimerAttachmentRepository;
        $this->timerRepository = $timerRepository;
        $this->emailSentDocketRecipientRepository = $emailSentDocketRecipientRepository;
        $this->emailSentDocketValueRepository = $emailSentDocketValueRepository;
        $this->emailSentDocketImageValueRepository = $emailSentDocketImageValueRepository;
        $this->docketFieldGridValueRepository = $docketFieldGridValueRepository;
        $this->docketFieldGridLabelRepository = $docketFieldGridLabelRepository;
        $this->yesNoDocketsFieldRepository = $yesNoDocketsFieldRepository;
        $this->sentEmailAttachmentRepository = $sentEmailAttachmentRepository;
        $this->sentEmailDocketInvoiceRepository = $sentEmailDocketInvoiceRepository;
        $this->emailSentDocketUnitRateValueRepository = $emailSentDocketUnitRateValueRepository;
        $this->emailSentDocketTallyUnitRateValRepository = $emailSentDocketTallyUnitRateValRepository;
        $this->emailSentDocManualTimerRepository = $emailSentDocManualTimerRepository;
        $this->emailSentDocManualTimerBrkRepository = $emailSentDocManualTimerBrkRepository;
        $this->docketProjectRepository = $docketProjectRepository;
        $this->sentDocketProjectRepository = $sentDocketProjectRepository;
        $this->folderItemRepository = $folderItemRepository;
        $this->sentEmailDocValYesNoValueRepository = $sentEmailDocValYesNoValueRepository;
        $this->sentDocValYesNoValueRepository = $sentDocValYesNoValueRepository;
        $this->sentDocketRecipientApprovalRepository = $sentDocketRecipientApprovalRepository;
        $this->sentDocketInvoiceRepository = $sentDocketInvoiceRepository;
        $this->userNotificationRepository = $userNotificationRepository;
        $this->documentThemeRepository = $documentThemeRepository;
        $this->assignedInvoiceRepository = $assignedInvoiceRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceFieldRepository = $invoiceFieldRepository;
        $this->emailSentInvoiceRepository = $emailSentInvoiceRepository;
        $this->emailClientRepository = $emailClientRepository;
        $this->emailSentInvoicePaymentDetailRepository = $emailSentInvoicePaymentDetailRepository;
        $this->sentEInvoiceAttachedEDocketRepository = $sentEInvoiceAttachedEDocketRepository;
        $this->emailSentInvoiceValueRepository = $emailSentInvoiceValueRepository;
        $this->emailSentInvoiceImageRepository = $emailSentInvoiceImageRepository;
        $this->emailSentInvoiceDescriptionRepository = $emailSentInvoiceDescriptionRepository;
        $this->sentInvoiceAttachedDocketRepository = $sentInvoiceAttachedDocketRepository;
        $this->sentInvoiceImageValueRepository = $sentInvoiceImageValueRepository;
        $this->companyXeroRepository = $companyXeroRepository;
        $this->sentInvoiceXeroRepository = $sentInvoiceXeroRepository;
        $this->sentXeroInvoiceSettingRepository = $sentXeroInvoiceSettingRepository;
        $this->invoiceXeroSettingRepository = $invoiceXeroSettingRepository;
        $this->synXeroContactRepository = $synXeroContactRepository;
        $this->firebaseApi = $firebaseApi;
        $this->messagesGroupUserRepository = $messagesGroupUserRepository;
        $this->messagesRecipientsRepository = $messagesRecipientsRepository;
        $this->messagesRepository = $messagesRepository;
        $this->messagesGroupRepository = $messagesGroupRepository;
        $this->timerSettingRepository = $timerSettingRepository;
        $this->timerRepository = $timerRepository;
        $this->timerClientRepository = $timerClientRepository;
        $this->timerImageRepository = $timerImageRepository;
        $this->timerCommentRepository = $timerCommentRepository;
        $this->timerLogRepository = $timerLogRepository;
        $this->timerAttachedTagRepository = $timerAttachedTagRepository;
        $this->clientRepository = $clientRepository;
        $this->appInfoRepository = $appInfoRepository;
        $this->sentDocketRecipientRepository = $sentDocketRecipientRepository;
        $this->userRepository = $userRepository;
        $this->docketDraftRepository = $docketDraftRepository;
        $this->sentInvoiceRepository = $sentInvoiceRepository;
        $this->sentInvoiceDescriptionRepository = $sentInvoiceDescriptionRepository;
        $this->sentInvoiceValueRepository = $sentInvoiceValueRepository;
        $this->sentInvoicePaymentDetailRepository = $sentInvoicePaymentDetailRepository;
        $this->sentDocketRejectRepository = $sentDocketRejectRepository;
        $this->appleSubscriptionRepository = $appleSubscriptionRepository;
        $this->yesNoFieldRepository = $yesNoFieldRepository;
        $this->docketFieldGridRepository = $docketFieldGridRepository;
        $this->leaveRepository = $leaveRepository;
        $this->invoiceSettingRepository = $invoiceSettingRepository;
        $this->machineRepository = $machineRepository;
        $this->assignDocketUserRepository = $assignDocketUserRepository;
        $this->docketDraftsAssignRepository = $docketDraftsAssignRepository;
        $this->assignDocketUserConnectionRepository = $assignDocketUserConnectionRepository;
    }
}
