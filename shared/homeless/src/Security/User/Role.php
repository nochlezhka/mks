<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Security\User;

final class Role
{
    public const string EMPLOYEE = 'ROLE_EMPLOYEE';
    public const string ALLOWED_TO_SWITCH = 'ROLE_ALLOWED_TO_SWITCH';

    public const string SONATA_ADMIN = 'ROLE_SONATA_ADMIN';
    public const string SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public const string SONATA_USER_ADMIN_USER_EDIT = 'ROLE_SONATA_USER_ADMIN_USER_EDIT';

    public const string APP_CLIENT_ADMIN_ALL = 'ROLE_APP_CLIENT_ADMIN_ALL';
    public const string APP_DOCUMENT_ADMIN_ALL = 'ROLE_APP_DOCUMENT_ADMIN_ALL';
    public const string APP_DOCUMENT_FILE_ADMIN_ALL = 'ROLE_APP_DOCUMENT_FILE_ADMIN_ALL';
    public const string APP_CONTRACT_ADMIN_ALL = 'ROLE_APP_CONTRACT_ADMIN_ALL';
    public const string APP_CONTRACT_ITEM_ADMIN_ALL = 'ROLE_APP_CONTRACT_ITEM_ADMIN_ALL';
    public const string APP_CERTIFICATE_ADMIN_ALL = 'ROLE_APP_CERTIFICATE_ADMIN_ALL';
    public const string APP_SHELTER_HISTORY_ADMIN_ALL = 'ROLE_APP_SHELTER_HISTORY_ADMIN_ALL';
    public const string APP_RESIDENT_FORM_RESPONSE_ADMIN_ALL = 'ROLE_APP_RESIDENT_FORM_RESPONSE_ADMIN_ALL';
    public const string APP_GENERATED_DOCUMENT_ADMIN_ALL = 'ROLE_APP_GENERATED_DOCUMENT_ADMIN_ALL';
    public const string APP_NOTE_ADMIN_ALL = 'ROLE_APP_NOTE_ADMIN_ALL';
    public const string APP_NOTICE_ADMIN_ALL = 'ROLE_APP_NOTICE_ADMIN_ALL';
    public const string APP_SERVICE_ADMIN_ALL = 'ROLE_APP_SERVICE_ADMIN_ALL';
}
