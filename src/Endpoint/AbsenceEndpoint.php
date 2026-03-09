<?php

namespace Vogelyt\AbsenceIoClient\Endpoint;

/**
 * Absence endpoint for managing absence records.
 */
class AbsenceEndpoint extends AbstractEndpoint
{
    // API uses plural path for absences
    protected string $entityName = 'absences';
}
