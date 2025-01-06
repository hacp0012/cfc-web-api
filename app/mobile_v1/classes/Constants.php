<?php

namespace App\mobile_v1\classes;

class Constants
{
  const GROUPS_OWNER      = 'OWNERS';
  const GROUPS_USER       = 'USERS';
  const GROUPS_DOCUMENT   = 'DOCUMENTS';
  const GROUPS_ECHO       = 'ECHOS';
  const GROUPS_COM        = 'COMMUNICATIONS';
  const GROUPS_TEACHING   = 'TEACHING';

  const PASSWORD          = '0000';

  const IMAGE_UPLOAD_NAME = 'picture';
  const IMAGE_UPLOAD_SIZE = 9 * 1024;
  const MAX_COMMENTS_PER_REQUEST = 18;
  const MAX_CONTENTS_PER_REQUEST_AT_HOME = 18;
}
